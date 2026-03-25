<?php

namespace Modules\Subscription\Http\Controllers\Frontend;

use App\Helpers\ModuleMetaData;
use App\Helpers\PaymentGatewayRenderHelper;
use App\Mail\BasicMail;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Modules\JobPost\Entities\JobRequest;
use Modules\Subscription\Entities\SellerSubscription;
use Auth;
use Modules\Subscription\Entities\Subscription;
use Modules\Subscription\Entities\SubscriptionCoupon;
use Modules\Subscription\Entities\SubscriptionHistory;
use Modules\Wallet\Entities\Wallet;
use Xgenious\Paymentgateway\Facades\XgPaymentGateway;


class SellerSubsController extends Controller
{

    private const CANCEL_ROUTE = 'seller.subscription.payment.cancel.static';
    private const SUCCESS_ROUTE = 'seller.subscription.payment.success';

    protected function subscription_renew_payment_cancel(){
        return redirect()->route('frontend.subscription.payment.cancel.static');
    }

    public function subscription_renew_payment_cancel_static()
    {
        return view('subscription::frontend.subscription.payment.renew-payment-cancel-static');
    }

    public function subscription_renew_payment_success($id)
    {
        $subscription_id = substr($id,30,-30);
        $subscription_details = SellerSubscription::find($subscription_id);
        return view('subscription::frontend.subscription.payment.renew-success')->with(['subscription_details' => $subscription_details]);
    }

    public function subscriptions()
    {
        $seller_id = Auth::guard('web')->user()->id;
        $subscription = SellerSubscription::where('seller_id', $seller_id)->first();
        $all_subscription_list = Subscription::where('status',1)->get();
        $subscription_history = SubscriptionHistory::where('seller_id', $seller_id)->latest()->paginate(10);
        return view('subscription::frontend.seller.subscriptions', compact('subscription', 'subscription_history', 'all_subscription_list'));
    }

    public function sub_renew(Request $request)
    {

        $request->validate([
            'selected_payment_gateway' => 'required',
        ]);

        $renew_subscription = Subscription::find($request->renew_subscription_id);
        if (!isset($renew_subscription)) {
            toastr_warning(__('subscription not found'));
            return back();
        }

        $seller_id = Auth::guard('web')->user()->id;
        $seller_email = Auth::guard('web')->user()->email;
        $seller_name = Auth::guard('web')->user()->name;
        $user_name = Auth::guard('web')->user()->name;
        $user_email = Auth::guard('web')->user()->email;

        // find seller subscription
        $seller_subscription = SellerSubscription::where('subscription_id', $renew_subscription->id)->where('seller_id', $seller_id)->first();
        if (!isset($seller_subscription)) {
            toastr_warning(__('subscription not found'));
            return back();
        }


        // check free plan
        if ($seller_subscription->price == 0) {
            toastr_warning(__('You have already availed the free plan for this month. Free plans are not eligible for renewal.'));
            return back();
        }


        // Check if subscription exists and expire date is within the specified renewal days
        if (!empty($seller_subscription)) {
            $expireDate = Carbon::parse($seller_subscription->expire_date);
            $currentDate = now();
            if($expireDate <= $currentDate) {
                toastr_warning(__('Your Subscription Has Expired'));
                return back();
            }
        } else {
            toastr_warning(__('Subscription Not Found'));
            return back();
        }


        // update start
        if ($seller_subscription->type == 'monthly') {
            $expire_date = Carbon::now()->addDays(30);
            $connect = $seller_subscription->connect;
            $service = $seller_subscription->service;
            $job = $seller_subscription->job;
        } elseif ($seller_subscription->type == 'yearly') {
            $expire_date = Carbon::now()->addDays(365);
            $connect = $seller_subscription->connect;
            $service = $seller_subscription->service;
            $job = $seller_subscription->job;
        } elseif ($seller_subscription->type == 'lifetime') {
            $expire_date = Carbon::now()->addDays(3650);
            $connect = 1000000;
            $service = 1000000;
            $job = 1000000;
        }

        $price = $renew_subscription->price;
        $seller_subscription_history = SubscriptionHistory::where('subscription_id', $seller_subscription->subscription_id)
            ->where('expire_date', '>', now())
            ->where('seller_id', $seller_subscription->seller_id)
            ->latest()->first();

        //todo: check payment gateway is wallet or not
        if (moduleExists("Wallet")){
            if ($request->selected_payment_gateway === 'wallet') {
                $wallet_balance = Wallet::select('balance')->where('buyer_id', $seller_id)->first();

                //check wallet has or not
                if(is_null($wallet_balance)){
                    toastr_warning(__('wallet not enabled. make your initial deposit to enable your wallet'));
                    return back();
                }

                if ($wallet_balance->balance >= $seller_subscription->price) {
                    // Renew subscription update
                    $seller_subscription->update([
                        'payment_status' => 'complete',
                        'payment_gateway' => 'wallet',
                        'expire_date' => $expire_date,
                        'connect' => ($renew_subscription->connect + $connect),
                        'service' => ($renew_subscription->service + $service),
                        'job' => ($renew_subscription->job + $job),
                        'price' => $price,
                        'status' => 1,
                    ]);


                    //  subscription history create
                    if (!empty($seller_subscription)){
                        SubscriptionHistory::create([
                            'subscription_id' => $seller_subscription->subscription_id,
                            'seller_id' => $seller_subscription->seller_id,
                            'type' => $seller_subscription->type,
                            'payment_status' => $seller_subscription->payment_status,
                            'payment_gateway' => $seller_subscription->payment_gateway,
                            'expire_date' => $seller_subscription->expire_date,
                            'connect' => $seller_subscription->connect,
                            'service' => $seller_subscription->service,
                            'job' => $seller_subscription->job,
                            'price' => $seller_subscription->price,
                            'coupon_code' => $seller_subscription_history->coupon_code,
                            'coupon_type' => $seller_subscription_history->coupon_type,
                            'coupon_amount' => $seller_subscription_history->coupon_amount,
                            'status' => 1,
                        ]);
                    }

                    // update balance
                    Wallet::where('buyer_id', $seller_id)->update([
                        'balance' => $wallet_balance->balance - $price,
                    ]);

                    //Send order email to admin and seller
                    try {
                        $connect = $seller_subscription->type =='lifetime' ? __("No Limit") : $connect;
                        $service = $seller_subscription->type =='lifetime' ? __("No Limit") : $service;
                        $job = $seller_subscription->type =='lifetime' ? __("No Limit") : $job;
                        $message = get_static_option('renew_subscription_seller_message') ?? '';

                        $message = str_replace(["@type","@price","@connect","@service","@job"],[$seller_subscription->type,float_amount_with_currency_symbol($seller_subscription->price),$connect,$service, $job],$message);
                        Mail::to($seller_email)->send(new BasicMail([
                            'subject' =>get_static_option('renew_subscription_email_subject') ?? __('Renew Subscription'),
                            'message' => $message
                        ]));

                        $message = get_static_option('renew_subscription_admin_message') ?? '';
                        $message = str_replace(["@type","@price","@connect","@service","@job","@seller_name","@seller_email"],[$seller_subscription->type,float_amount_with_currency_symbol($seller_subscription->price),$connect, $service, $job,$seller_name,$seller_email],$message);
                        Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                            'subject' =>get_static_option('renew_subscription_email_subject') ?? __('Renew Subscription'),
                            'message' => $message
                        ]));

                    } catch (\Exception $e) {
                        \Toastr::error($e->getMessage());
                    }

                    toastr_success(__('Your subscription renewed successfully'));
                    return back();

                } else {
                    toastr_warning(__('Your wallet balance is not sufficient to renew this subscription'));
                }

                return back();
            }
        }


        // variable for all payment gateway
        $global_currency = get_static_option('site_global_currency');
        $usd_conversion_rate =  get_static_option('site_' . strtolower($global_currency) . '_to_usd_exchange_rate');
        $inr_exchange_rate = getenv('INR_EXCHANGE_RATE');
        $ngn_exchange_rate = getenv('NGN_EXCHANGE_RATE');
        $zar_exchange_rate = getenv('ZAR_EXCHANGE_RATE');
        $brl_exchange_rate = getenv('BRL_EXCHANGE_RATE');
        $idr_exchange_rate = getenv('IDR_EXCHANGE_RATE');
        $myr_exchange_rate = getenv('MYR_EXCHANGE_RATE');

        $last_subscription_id = $seller_subscription->id;

        // if Manual payment and other payment gateway
        if($request->selected_payment_gateway === 'manual_payment') {
            $random_order_id_1 = Str::random(30);
            $random_order_id_2 = Str::random(30);
            $new_order_id = $random_order_id_1.$seller_subscription->id.$random_order_id_2;

            if($request->hasFile('manual_payment_image')){
                $manual_payment_image = $request->manual_payment_image;
                $img_ext = $manual_payment_image->extension();

                $manual_payment_image_name = 'manual_attachment_'.time().'.'.$img_ext;
                if(in_array($img_ext,['jpg','jpeg','png','webp','svg','pdf'])){
                    $manual_image_path = 'assets/uploads/subscription/manual-payment/';

                    // file scan start
                    $uploaded_file = $manual_payment_image;
                    $file_extension = $uploaded_file->getClientOriginalExtension();
                    if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $processed_image = Image::make($uploaded_file);
                        $image_default_width = $processed_image->width();
                        $image_default_height = $processed_image->height();

                        $processed_image->resize($image_default_width, $image_default_height, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $processed_image->save($manual_image_path . $manual_payment_image_name);
                    }else{
                        $manual_payment_image->move($manual_image_path,$manual_payment_image_name);
                    } // file scan end

                    // Renew subscription update
                    $seller_subscription->update([
                        'payment_status' => '',
                        'payment_gateway' => 'manual_payment',
                        'expire_date' => $expire_date,
                        'connect' => ($renew_subscription->connect + $connect),
                        'service' => ($renew_subscription->service + $service),
                        'job' => ($renew_subscription->job + $job),
                        'price' => $price,
                        'status' => 1,
                    ]);

                    //  subscription history create
                    if (!empty($seller_subscription)){
                        SubscriptionHistory::create([
                            'subscription_id' => $seller_subscription->subscription_id,
                            'seller_id' => $seller_subscription->seller_id,
                            'type' => $seller_subscription->type,
                            'payment_status' => $seller_subscription->payment_status,
                            'payment_gateway' => $seller_subscription->payment_gateway,
                            'expire_date' => $seller_subscription->expire_date,
                            'connect' => $seller_subscription->connect,
                            'service' => $seller_subscription->service,
                            'job' => $seller_subscription->job,
                            'price' => $seller_subscription->price,
                            'coupon_code' => $seller_subscription_history->coupon_code,
                            'coupon_type' => $seller_subscription_history->coupon_type,
                            'coupon_amount' => $seller_subscription_history->coupon_amount,
                            'status' => 1,
                        ]);
                    }

                }else{
                    return back()->with(['msg' => __('image type not supported'),'type' => 'danger']);
                }
            }

            //Send order email to admin and seller
            try {
                $connect = $seller_subscription->type =='lifetime' ? __("No Limit") : $seller_subscription->connect;
                $service = $seller_subscription->type =='lifetime' ? __("No Limit") : $seller_subscription->service;
                $job = $seller_subscription->type =='lifetime' ? __("No Limit") : $seller_subscription->job;

                $message = get_static_option('renew_subscription_seller_message') ?? '';
                $message = str_replace(["@type","@price","@connect","@service","@job"],[$seller_subscription->type,float_amount_with_currency_symbol($price),$connect, $service, $job],$message);
                Mail::to($user_email)->send(new BasicMail([
                    'subject' =>get_static_option('renew_subscription_email_subject') ?? __('Renew Subscription'),
                    'message' => $message
                ]));

                $message = get_static_option('renew_subscription_admin_message') ?? '';
                $message = str_replace(["@type","@price","@connect","@service","@job","@seller_name","@email"],[$seller_subscription->type,float_amount_with_currency_symbol($price),$connect,$service, $job,$user_name,$user_email],$message);
                Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                    'subject' =>get_static_option('renew_subscription_email_subject') ?? __('Renew Subscription'),
                    'message' => $message
                ]));

            } catch (\Exception $e) {
                \Toastr::error($e->getMessage());
            }
            return redirect()->route('seller.subscription.renew.payment.success',$new_order_id);

        }else{

            //  subscription history create
            if (!empty($seller_subscription)){
                $create_history =   SubscriptionHistory::create([
                    'subscription_id' => $seller_subscription->subscription_id,
                    'seller_id' => $seller_subscription->seller_id,
                    'type' => $seller_subscription->type,
                    'payment_status' => '',
                    'payment_gateway' => $request->selected_payment_gateway,
                    'expire_date' => $expire_date,
                    'connect' => ($seller_subscription->connect + $renew_subscription->connect),
                    'service' => ($seller_subscription->service + $renew_subscription->service),
                    'job' => ($seller_subscription->job + $renew_subscription->job),
                    'price' => $renew_subscription->price,
                    'coupon_code' => $seller_subscription_history->coupon_code,
                    'coupon_type' => $seller_subscription_history->coupon_type,
                    'coupon_amount' => $seller_subscription_history->coupon_amount,
                    'status' => 1,
                ]);
            }
            $last_subscription_history = $create_history->id;

            if ($request->selected_payment_gateway === 'paypal') {
                $paypal_mode = getenv('PAYPAL_MODE');
                $client_id = $paypal_mode === 'sandbox' ? getenv('PAYPAL_SANDBOX_CLIENT_ID') : getenv('PAYPAL_LIVE_CLIENT_ID');
                $client_secret = $paypal_mode === 'sandbox' ? getenv('PAYPAL_SANDBOX_CLIENT_SECRET') : getenv('PAYPAL_LIVE_CLIENT_SECRET');
                $app_id = $paypal_mode === 'sandbox' ? getenv('PAYPAL_SANDBOX_APP_ID') : getenv('PAYPAL_LIVE_APP_ID');

                $paypal = XgPaymentGateway::paypal();

                $paypal->setClientId($client_id); // provide sandbox id if payment env set to true, otherwise provide live credentials
                $paypal->setClientSecret($client_secret); // provide sandbox id if payment env set to true, otherwise provide live credentials
                $paypal->setAppId($app_id); // provide sandbox id if payment env set to true, otherwise provide live credentials
                $paypal->setCurrency($global_currency);
                $paypal->setEnv($paypal_mode === 'sandbox'); //env must set as boolean, string will not work
                $paypal->setExchangeRate($usd_conversion_rate); // if INR not set as currency

                $redirect_url = $paypal->charge_customer([
                    'amount' => $price, // amount you want to charge from customer
                    'title' => $seller_subscription->type, // payment title
                    'description' => 'Subscription', // payment description
                    'ipn_url' => route('seller.renew.paypal.ipn.subs'), //you will get payment response in this route
                    'order_id' => $last_subscription_id, // your order number
                    'track' => \Str::random(36), // a random number to keep track of your payment
                    'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id), //payment gateway will redirect here if the payment is failed
                    'success_url' => route(self::SUCCESS_ROUTE,$last_subscription_id), // payment gateway will redirect here after success
                    'email' => $user_email, // user email
                    'name' => $user_name, // user name
                    'payment_type' => 'order', // which kind of payment your are receving from customer
                ]);
                session()->put('order_id',$last_subscription_id);
                session()->put('history_id',$last_subscription_history);
                return $redirect_url;
            }
            elseif($request->selected_payment_gateway === 'paytm'){

                $paytm_merchant_id = getenv('PAYTM_MERCHANT_ID');
                $paytm_merchant_key = getenv('PAYTM_MERCHANT_KEY');
                $paytm_merchant_website = getenv('PAYTM_MERCHANT_WEBSITE') ?? 'WEBSTAGING';
                $paytm_channel = getenv('PAYTM_CHANNEL') ?? 'WEB';
                $paytm_industry_type = getenv('PAYTM_INDUSTRY_TYPE') ?? 'Retail';
                $paytm_env = getenv('PAYTM_ENVIRONMENT');

                $paytm = XgPaymentGateway::paytm();
                $paytm->setMerchantId($paytm_merchant_id);
                $paytm->setMerchantKey($paytm_merchant_key);
                $paytm->setMerchantWebsite($paytm_merchant_website);
                $paytm->setChannel($paytm_channel);
                $paytm->setIndustryType($paytm_industry_type);
                $paytm->setCurrency($global_currency);
                $paytm->setEnv($paytm_env === 'local'); // this must be type of boolean , string will not work
                $paytm->setExchangeRate($inr_exchange_rate); // if INR not set as currency

                $redirect_url = $paytm->charge_customer([
                    'amount' => $price,
                    'title' => $seller_subscription->type,
                    'description' => 'Subscription',
                    'ipn_url' => route('seller.renew.paytm.ipn.subs'),
                    'order_id' => $last_subscription_id,
                    'track' => \Str::random(36),
                    'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id),
                    'success_url' => route(self::SUCCESS_ROUTE,$last_subscription_id),
                    'email' => $user_email,
                    'name' => $user_name,
                    'payment_type' => 'order',
                ]);
                session()->put('order_id',$last_subscription_id);
                session()->put('history_id',$last_subscription_history);
                return $redirect_url;
            }
            elseif($request->selected_payment_gateway === 'mollie'){
                $mollie_key = getenv('MOLLIE_KEY');
                $mollie = XgPaymentGateway::mollie();
                $mollie->setApiKey($mollie_key);
                $mollie->setCurrency($global_currency);
                $mollie->setEnv(true); //env must set as boolean, string will not work
                $mollie->setExchangeRate($usd_conversion_rate); // if INR not set as currency

                $redirect_url = $mollie->charge_customer([
                    'amount' => $price,
                    'title' => $seller_subscription->type,
                    'description' => 'Subscription',
                    'ipn_url' => route('seller.renew.mollie.ipn.subs'),
                    'order_id' => $last_subscription_id,
                    'track' => \Str::random(36),
                    'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id),
                    'success_url' => route(self::SUCCESS_ROUTE,$last_subscription_id),
                    'email' => $user_email,
                    'name' => $user_name,
                    'payment_type' => 'order',
                ]);
                session()->put('order_id',$last_subscription_id);
                session()->put('history_id',$last_subscription_history);
                return $redirect_url;
            }
            elseif($request->selected_payment_gateway === 'stripe'){

                $stripe_public_key = getenv('STRIPE_PUBLIC_KEY');
                $stripe_secret_key = getenv('STRIPE_SECRET_KEY');
                $stripe = XgPaymentGateway::stripe();
                $stripe->setSecretKey($stripe_secret_key);
                $stripe->setPublicKey($stripe_public_key);
                $stripe->setCurrency($global_currency);
                $stripe->setEnv(true); //env must set as boolean, string will not work
                $stripe->setExchangeRate($usd_conversion_rate); // if INR not set as currency

                $redirect_url = $stripe->charge_customer([
                    'amount' => $price,
                    'title' => $seller_subscription->type,
                    'description' => 'Subscription',
                    'ipn_url' => route('seller.renew.stripe.ipn.subs'),
                    'order_id' => $last_subscription_id,
                    'track' => \Str::random(36),
                    'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id),
                    'success_url' => route(self::SUCCESS_ROUTE,$last_subscription_id),
                    'email' => $user_email,
                    'name' => $user_name,
                    'payment_type' => 'order',
                ]);
                session()->put('order_id',$last_subscription_id);
                session()->put('history_id',$last_subscription_history);
                return $redirect_url;
            }
            elseif($request->selected_payment_gateway === 'razorpay'){

                $razorpay_api_key = getenv('RAZORPAY_API_KEY');
                $razorpay_api_secret = getenv('RAZORPAY_API_SECRET');
                $razorpay = XgPaymentGateway::razorpay();
                $razorpay->setApiKey($razorpay_api_key);
                $razorpay->setApiSecret($razorpay_api_secret);
                $razorpay->setCurrency($global_currency);
                $razorpay->setEnv(true); //env must set as boolean, string will not work
                $razorpay->setExchangeRate($inr_exchange_rate); // if INR not set as currency

                $redirect_url = $razorpay->charge_customer([
                    'amount' => $price,
                    'title' => $seller_subscription->type,
                    'description' => 'Subscription',
                    'ipn_url' => route('seller.renew.razorpay.ipn.subs'),
                    'order_id' => $last_subscription_id,
                    'track' => \Str::random(36),
                    'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id),
                    'success_url' => route(self::SUCCESS_ROUTE,$last_subscription_id),
                    'email' => $user_email,
                    'name' => $user_name,
                    'payment_type' => 'order',
                ]);
                session()->put('order_id',$last_subscription_id);
                session()->put('history_id',$last_subscription_history);
                return $redirect_url;
            }
            elseif($request->selected_payment_gateway === 'flutterwave'){

                $flutterwave_public_key = getenv("FLW_PUBLIC_KEY");
                $flutterwave_secret_key = getenv("FLW_SECRET_KEY");
                $flutterwave_secret_hash = getenv("FLW_SECRET_HASH");

                $flutterwave = XgPaymentGateway::flutterwave();
                $flutterwave->setPublicKey($flutterwave_public_key);
                $flutterwave->setSecretKey($flutterwave_secret_key);
                $flutterwave->setCurrency($global_currency);
                $flutterwave->setEnv(true); //env must set as boolean, string will not work
                $flutterwave->setExchangeRate($usd_conversion_rate); // if NGN not set as currency

                $redirect_url = $flutterwave->charge_customer([
                    'amount' => $price,
                    'title' => $seller_subscription->type,
                    'description' => 'Subscription',
                    'ipn_url' => route('seller.renew.flutterwave.ipn.subs'),
                    'order_id' => $last_subscription_id,
                    'track' => \Str::random(36),
                    'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id),
                    'success_url' => route(self::SUCCESS_ROUTE,$last_subscription_id),
                    'email' => $user_email,
                    'name' => $user_name,
                    'payment_type' => 'order',
                ]);
                session()->put('order_id',$last_subscription_id);
                session()->put('history_id',$last_subscription_history);
                return $redirect_url;
            }
            elseif($request->selected_payment_gateway === 'paystack'){

                $paystack_public_key = getenv('PAYSTACK_PUBLIC_KEY');
                $paystack_secret_key = getenv('PAYSTACK_SECRET_KEY');
                $paystack_merchant_email = getenv('MERCHANT_EMAIL');

                $paystack = XgPaymentGateway::paystack();
                $paystack->setPublicKey($paystack_public_key);
                $paystack->setSecretKey($paystack_secret_key);
                $paystack->setMerchantEmail($paystack_merchant_email);
                $paystack->setCurrency($global_currency);
                $paystack->setEnv(true); //env must set as boolean, string will not work
                $paystack->setExchangeRate($ngn_exchange_rate); // if NGN not set as currency

                $redirect_url =$paystack->charge_customer([
                    'amount' => $price,
                    'title' => $seller_subscription->type,
                    'description' => 'Subscription',
                    'ipn_url' => route('seller.renew.paystack.ipn.subs'),
                    'order_id' => $last_subscription_id,
                    'track' => \Str::random(36),
                    'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id),
                    'success_url' => route(self::SUCCESS_ROUTE,$last_subscription_id),
                    'email' =>  $user_email,
                    'name' => $user_name,
                    'payment_type' => 'order',
                ]);
                session()->put('order_id',$last_subscription_id);
                session()->put('history_id',$last_subscription_history);
                return $redirect_url;
            }
            elseif($request->selected_payment_gateway === 'payfast'){

                $random_order_id_1 = Str::random(30);
                $random_order_id_2 = Str::random(30);
                $payfast_merchant_id = getenv('PF_MERCHANT_ID');
                $payfast_merchant_key = getenv('PF_MERCHANT_KEY');
                $payfast_passphrase = getenv('PAYFAST_PASSPHRASE');
                $payfast_env = getenv('PF_MERCHANT_ENV') === 'true';

                $payfast = XgPaymentGateway::payfast();
                $payfast->setMerchantId($payfast_merchant_id);
                $payfast->setMerchantKey($payfast_merchant_key);
                $payfast->setPassphrase($payfast_passphrase);
                $payfast->setCurrency($global_currency);
                $payfast->setEnv($payfast_env); //env must set as boolean, string will not work
                $payfast->setExchangeRate($zar_exchange_rate); // if ZAR not set as currency

                $redirect_url = $payfast->charge_customer([
                    'amount' => $price,
                    'title' => $seller_subscription->type,
                    'description' => 'Subscription',
                    'ipn_url' => route('seller.renew.payfast.ipn.subs'),
                    'order_id' => $last_subscription_id,
                    'track' => \Str::random(36),
                    'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id),
                    'success_url' => route(self::SUCCESS_ROUTE,$random_order_id_1.$last_subscription_id.$random_order_id_2),
                    'email' => $user_email,
                    'name' =>  $user_name,
                    'payment_type' => 'order',
                ]);
                session()->put('order_id',$last_subscription_id);
                session()->put('history_id',$last_subscription_history);
                return $redirect_url;
            }
            elseif($request->selected_payment_gateway === 'cashfree'){

                $cashfree_env = getenv('CASHFREE_TEST_MODE') === 'true';
                $cashfree_app_id = getenv('CASHFREE_APP_ID');
                $cashfree_secret_key = getenv('CASHFREE_SECRET_KEY');

                $cashfree = XgPaymentGateway::cashfree();
                $cashfree->setAppId($cashfree_app_id);
                $cashfree->setSecretKey($cashfree_secret_key);
                $cashfree->setCurrency($global_currency);
                $cashfree->setEnv($cashfree_env); //true means sandbox, false means live , //env must set as boolean, string will not work
                $cashfree->setExchangeRate($inr_exchange_rate); // if INR not set as currency

                $redirect_url = $cashfree->charge_customer([
                    'amount' => $price,
                    'title' => $seller_subscription->type,
                    'description' => 'Subscription',
                    'ipn_url' => route('seller.renew.cashfree.ipn.subs'),
                    'order_id' => $last_subscription_id,
                    'track' => \Str::random(36),
                    'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id),
                    'success_url' => route(self::SUCCESS_ROUTE,$last_subscription_id),
                    'email' => $user_email,
                    'name' =>  $user_name,
                    'payment_type' => 'order',
                ]);
                session()->put('order_id',$last_subscription_id);
                session()->put('history_id',$last_subscription_history);
                return $redirect_url;
            }
            elseif($request->selected_payment_gateway === 'instamojo'){

                $instamojo_client_id = getenv('INSTAMOJO_CLIENT_ID');
                $instamojo_client_secret = getenv('INSTAMOJO_CLIENT_SECRET');
                $instamojo_env = getenv('INSTAMOJO_TEST_MODE') === 'true';

                $instamojo = XgPaymentGateway::instamojo();
                $instamojo->setClientId($instamojo_client_id);
                $instamojo->setSecretKey($instamojo_client_secret);
                $instamojo->setCurrency($global_currency);
                $instamojo->setEnv($instamojo_env); //true mean sandbox mode , false means live mode //env must set as boolean, string will not work
                $instamojo->setExchangeRate($inr_exchange_rate); // if INR not set as currency

                $redirect_url = $instamojo->charge_customer([
                    'amount' => $price,
                    'title' => $seller_subscription->type,
                    'description' => 'Subscription',
                    'ipn_url' => route('seller.renew.instamojo.ipn.subs'),
                    'order_id' => $last_subscription_id,
                    'track' => 'asdfasdfsdf',
                    'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id),
                    'success_url' => route(self::SUCCESS_ROUTE,$last_subscription_id),
                    'email' => $user_email,
                    'name' => $user_name,
                    'payment_type' => 'order',
                ]);
                session()->put('order_id',$last_subscription_id);
                session()->put('history_id',$last_subscription_history);
                return $redirect_url;
            }
            elseif($request->selected_payment_gateway === 'marcadopago'){

                $mercadopago_client_id = getenv('MERCADO_PAGO_CLIENT_ID');
                $mercadopago_client_secret = getenv('MERCADO_PAGO_CLIENT_SECRET');
                $mercadopago_env =  getenv('MERCADO_PAGO_TEST_MOD') === 'true';

                $marcadopago = XgPaymentGateway::marcadopago();
                $marcadopago->setClientId($mercadopago_client_id);
                $marcadopago->setClientSecret($mercadopago_client_secret);
                $marcadopago->setCurrency($global_currency);
                $marcadopago->setExchangeRate($brl_exchange_rate); // if BRL not set as currency, you must have to provide exchange rate for it
                $marcadopago->setEnv($mercadopago_env); ////true mean sandbox mode , false means live mode
                ///
                $redirect_url = $marcadopago->charge_customer([
                    'amount' => $price,
                    'title' => $seller_subscription->type,
                    'description' => 'Subscription',
                    'ipn_url' => route('seller.renew.marcadopago.ipn.subs'),
                    'order_id' => $last_subscription_id,
                    'track' => \Str::random(36),
                    'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id),
                    'success_url' => route(self::SUCCESS_ROUTE,$last_subscription_id),
                    'email' => $user_email,
                    'name' => $user_name,
                    'payment_type' => 'order',
                ]);
                session()->put('order_id',$last_subscription_id);
                session()->put('history_id',$last_subscription_history);
                return $redirect_url;
            }
            elseif($request->selected_payment_gateway === 'midtrans'){

                $midtrans_env =  getenv('MIDTRANS_ENVAIRONTMENT') === 'true';
                $midtrans_server_key = getenv('MIDTRANS_SERVER_KEY');
                $midtrans_client_key = getenv('MIDTRANS_CLIENT_KEY');

                $midtrans = XgPaymentGateway::midtrans();
                $midtrans->setClientKey($midtrans_client_key);
                $midtrans->setServerKey($midtrans_server_key);
                $midtrans->setCurrency($global_currency);
                $midtrans->setEnv($midtrans_env); //true mean sandbox mode , false means live mode
                $midtrans->setExchangeRate($idr_exchange_rate); // if IDR not set as currency

                $redirect_url = $midtrans->charge_customer([
                    'amount' => $price,
                    'title' => $seller_subscription->type,
                    'description' => 'Subscription',
                    'ipn_url' => route('seller.renew.midtrans.ipn.subs'),
                    'order_id' => $last_subscription_id,
                    'track' => \Str::random(36),
                    'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id),
                    'success_url' => route(self::SUCCESS_ROUTE,$last_subscription_id),
                    'email' => $user_email,
                    'name' => $user_name,
                    'payment_type' => 'order',
                ]);
                session()->put('order_id',$last_subscription_id);
                session()->put('history_id',$last_subscription_history);
                return $redirect_url;
            }
            elseif($request->selected_payment_gateway === 'squareup'){

                $squareup_env =  !empty(get_static_option('squareup_test_mode'));
                $squareup_location_id = get_static_option('squareup_location_id');
                $squareup_access_token = get_static_option('squareup_access_token');
                $squareup_application_id = get_static_option('squareup_application_id');

                $squareup = XgPaymentGateway::squareup();
                $squareup->setLocationId($squareup_location_id);
                $squareup->setAccessToken($squareup_access_token);
                $squareup->setApplicationId($squareup_application_id);
                $squareup->setCurrency($global_currency);
                $squareup->setEnv($squareup_env);
                $squareup->setExchangeRate($usd_conversion_rate); // if USD not set as currency

                $redirect_url = $squareup->charge_customer([
                    'amount' => $price,
                    'title' => $seller_subscription->type,
                    'description' => 'Subscription',
                    'ipn_url' => route('seller.renew.squareup.ipn.subs'),
                    'order_id' => $last_subscription_id,
                    'track' => \Str::random(36),
                    'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id),
                    'success_url' => route(self::SUCCESS_ROUTE,$last_subscription_id),
                    'email' => $user_email,
                    'name' => $user_name,
                    'payment_type' => 'order',
                ]);
                session()->put('order_id',$last_subscription_id);
                session()->put('history_id',$last_subscription_history);
                return $redirect_url;
            }
            elseif($request->selected_payment_gateway === 'cinetpay'){

                $cinetpay_env =  !empty(get_static_option('cinetpay_test_mode'));
                $cinetpay_site_id = get_static_option('cinetpay_site_id');
                $cinetpay_app_key = get_static_option('cinetpay_app_key');

                $cinetpay = XgPaymentGateway::cinetpay();
                $cinetpay->setAppKey($cinetpay_app_key);
                $cinetpay->setSiteId($cinetpay_site_id);
                $cinetpay->setCurrency($global_currency);
                $cinetpay->setEnv($cinetpay_env);
                $cinetpay->setExchangeRate($usd_conversion_rate); // if ['XOF', 'XAF', 'CDF', 'GNF', 'USD'] not set as currency

                $redirect_url = $cinetpay->charge_customer([
                    'amount' => $price,
                    'title' => $seller_subscription->type,
                    'description' => 'Subscription',
                    'ipn_url' => route('seller.renew.cinetpay.ipn.subs'),
                    'order_id' => $last_subscription_id,
                    'track' => \Str::random(36),
                    'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id),
                    'success_url' => route(self::SUCCESS_ROUTE,$last_subscription_id),
                    'email' => $user_email,
                    'name' => $user_name,
                    'payment_type' => 'order',
                ]);
                session()->put('order_id',$last_subscription_id);
                session()->put('history_id',$last_subscription_history);
                return $redirect_url;
            }
            elseif($request->selected_payment_gateway === 'paytabs'){

                $paytabs_env =  !empty(get_static_option('paytabs_test_mode'));
                $paytabs_region = get_static_option('paytabs_region');
                $paytabs_profile_id = get_static_option('paytabs_profile_id');
                $paytabs_server_key = get_static_option('paytabs_server_key');

                $paytabs = XgPaymentGateway::paytabs();
                $paytabs->setProfileId($paytabs_profile_id);
                $paytabs->setRegion($paytabs_region);
                $paytabs->setServerKey($paytabs_server_key);
                $paytabs->setCurrency($global_currency);
                $paytabs->setEnv($paytabs_env);
                $paytabs->setExchangeRate($usd_conversion_rate); // if ['AED','EGP','SAR','OMR','JOD','USD'] not set as currency

                $redirect_url = $paytabs->charge_customer([
                    'amount' => $price,
                    'title' => $seller_subscription->type,
                    'description' => 'Subscription',
                    'ipn_url' => route('seller.renew.paytabs.ipn.subs'),
                    'order_id' => $last_subscription_id,
                    'track' => \Str::random(36),
                    'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id),
                    'success_url' => route(self::SUCCESS_ROUTE,$last_subscription_id),
                    'email' => $user_email,
                    'name' => $user_name,
                    'payment_type' => 'order',
                ]);
                session()->put('order_id',$last_subscription_id);
                session()->put('history_id',$last_subscription_history);
                return $redirect_url;
            }
            elseif($request->selected_payment_gateway === 'billplz'){

                $billplz_env =  !empty(get_static_option('billplz_test_mode'));
                $billplz_key =  get_static_option('billplz_key');
                $billplz_xsignature =  get_static_option('billplz_xsignature');
                $billplz_collection_name =  get_static_option('billplz_collection_name');

                $billplz = XgPaymentGateway::billplz();
                $billplz->setKey($billplz_key);
                $billplz->setVersion('v4');
                $billplz->setXsignature($billplz_xsignature);
                $billplz->setCollectionName($billplz_collection_name);
                $billplz->setCurrency($global_currency);
                $billplz->setEnv($billplz_env);
                $billplz->setExchangeRate($myr_exchange_rate); // if ['MYR'] not set as currency
                $random_order_id_1 = Str::random(30);
                $random_order_id_2 = Str::random(30);
                $new_order_id = $random_order_id_1.$last_subscription_id.$random_order_id_2;

                $redirect_url = $billplz->charge_customer([
                    'amount' => $price,
                    'title' => $seller_subscription->type,
                    'description' => 'Subscription',
                    'ipn_url' => route('seller.renew.billplz.ipn.subs'),
                    'order_id' => $last_subscription_id,
                    'track' => \Str::random(36),
                    'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id),
                    'success_url' => route(self::SUCCESS_ROUTE,$new_order_id),
                    'email' => $user_email,
                    'name' => $user_name,
                    'payment_type' => 'order',
                ]);
                session()->put('order_id',$last_subscription_id);
                session()->put('history_id',$last_subscription_history);
                return $redirect_url;
            }
            elseif($request->selected_payment_gateway === 'zitopay'){

                $zitopay_env =  !empty(get_static_option('zitopay_test_mode'));
                $zitopay_username =  get_static_option('zitopay_username');

                $zitopay = XgPaymentGateway::zitopay();
                $zitopay->setUsername($zitopay_username);
                $zitopay->setCurrency($global_currency);
                $zitopay->setEnv($zitopay_env);
                $zitopay->setExchangeRate($usd_conversion_rate);

                $random_order_id_1 = Str::random(30);
                $random_order_id_2 = Str::random(30);
                $new_order_id = $random_order_id_1.$last_subscription_id.$random_order_id_2;

                $redirect_url = $zitopay->charge_customer([
                    'amount' => $price,
                    'title' => $seller_subscription->type,
                    'description' => 'Subscription',
                    'ipn_url' => route('seller.renew.zitopay.ipn.subs'),
                    'order_id' => $last_subscription_id,
                    'track' => \Str::random(36),
                    'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id),
                    'success_url' => route(self::SUCCESS_ROUTE,$new_order_id),
                    'email' => $user_email,
                    'name' => $user_name,
                    'payment_type' => 'order',
                ]);
                session()->put('order_id',$last_subscription_id);
                session()->put('history_id',$last_subscription_history);
                return $redirect_url;
            }elseif ($request->selected_payment_gateway === 'kineticpay'){
                $kineticpay_env =  !empty(get_static_option('kineticpay_test_mode'));
                $kineticpay_username =  get_static_option('kineticpay_username');

                $kineticpay = XgPaymentGateway::kineticpay();
                $kineticpay->setMerchantKey($kineticpay_username);
                $kineticpay->setBank(request()->kineticpay_bank);
                $kineticpay->setCurrency($global_currency);
                $kineticpay->setEnv($kineticpay_env);
                $kineticpay->setExchangeRate($usd_conversion_rate);

                $random_order_id_1 = Str::random(30);
                $random_order_id_2 = Str::random(30);
                $new_order_id = $random_order_id_1.$last_subscription_id.$random_order_id_2;

                $redirect_url = $kineticpay->charge_customer([
                    'amount' => $price,
                    'title' => $seller_subscription->type,
                    'description' => 'Subscription',
                    'ipn_url' => route('seller.renew.kineticpay.ipn.subs'),
                    'order_id' => $last_subscription_id,
                    'track' => \Str::random(36),
                    'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id),
                    'success_url' => route(self::SUCCESS_ROUTE,$new_order_id),
                    'email' => $user_email,
                    'name' => $user_name,
                    'payment_type' => 'order',
                ]);
                session()->put('order_id',$last_subscription_id);
                session()->put('history_id',$last_subscription_history);
                return $redirect_url;
            }else {
                //todo check qixer meta data for new payment gateway
                $module_meta =  new ModuleMetaData();
                $list = $module_meta->getAllPaymentGatewayList();
                if (in_array($request->selected_payment_gateway,$list)){
                    //todo call the module payment gateway customerCharge function
                    $random_order_id_1 = Str::random(30);
                    $random_order_id_2 = Str::random(30);
                    $new_order_id = $random_order_id_1.$last_subscription_id.$random_order_id_2;

                    $customerChargeMethod =  $module_meta->getChargeCustomerMethodNameByPaymentGatewayName($request->selected_payment_gateway);
                    try {
                        $return_url = $customerChargeMethod([
                            'amount' => $price,
                            'title' => $seller_subscription->type,
                            'description' => 'Subscription',
                            'ipn_url' => route('seller.renew.zitopay.ipn.subs'),
                            'order_id' => $last_subscription_id,
                            'track' => \Str::random(36),
                            'cancel_url' => route(self::CANCEL_ROUTE,$last_subscription_id),
                            'success_url' => route(self::SUCCESS_ROUTE,$new_order_id),
                            'email' => $user_email,
                            'name' => $user_name,
                            'payment_type' => 'subscription',
                            'history_id' => $last_subscription_history
                        ]);

                        if(is_array($returned_val) && isset($returned_val['route'])){
                            $return_url = !empty($returned_val['route']) ? $returned_val['route'] : route('homepage');
                            return redirect()->away($return_url);
                        }
                    }catch (\Exception $e){
                        toastr_error( $e->getMessage());
                        return back();
                    }
                }
            }
        }

        toastr_warning(__('not found subscription'));
        return back();

    }
}