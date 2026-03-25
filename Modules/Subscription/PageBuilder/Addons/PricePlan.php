<?php


namespace Modules\Subscription\PageBuilder\Addons;

use App\Day;
use App\PageBuilder\Fields\ColorPicker;
use App\PageBuilder\Fields\Slider;
use App\PageBuilder\Fields\Text;
use App\PageBuilder\Traits\LanguageFallbackForPageBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Modules\Subscription\Entities\Subscription;

class PricePlan extends \App\PageBuilder\PageBuilderBase
{
    use LanguageFallbackForPageBuilder;

    public function preview_image()
    {
        return 'price_plan/price_plan_one.jpg';
    }

    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();
        $widget_saved_values = $this->get_settings();

        $output .= Text::get([
            'name' => 'title',
            'label' => __('Title'),
            'value' => $widget_saved_values['title'] ?? null,
        ]);
        $output .= ColorPicker::get([
            'name' => 'title_text_color',
            'label' => __('Title Text Color'),
            'value' => $widget_saved_values['title_text_color'] ?? null,
            'info' => __('select color you want to show in frontend'),
        ]);
        $output .= Text::get([
            'name' => 'subtitle',
            'label' => __('Subtitle'),
            'value' => $widget_saved_values['subtitle'] ?? null,
        ]);

        $output .= Slider::get([
            'name' => 'padding_top',
            'label' => __('Padding Top'),
            'value' => $widget_saved_values['padding_top'] ?? 260,
            'max' => 500,
        ]);
        $output .= Slider::get([
            'name' => 'padding_bottom',
            'label' => __('Padding Bottom'),
            'value' => $widget_saved_values['padding_bottom'] ?? 190,
            'max' => 500,
        ]);

        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }


    public function frontend_render(): string
    {
        if (!moduleExists('Subscription') || !Route::has('seller.subscription.buy')) {
            return '';
        }

        $settings = $this->get_settings();
        $title = $settings['title'];
        $title_text_color = $settings['title_text_color'];
        $explode = explode(" ", $title);
        $title_start = current($explode);
        $title_end = end($explode);
        $subtitle = $settings['subtitle'];
        $padding_top = $settings['padding_top'];
        $padding_bottom = $settings['padding_bottom'];
        $subscription_text = get_static_option('seller_buy_subscription_modal_title') ?? __('You must pay first to buy a subscription');
        $close_text = __('Close');
        $buy_now_text = __('Buy Now');
        $apply = __('Apply');
        $number_of_connect = get_static_option('set_number_of_connect',2);

        $connect_text = sprintf(__('Connect to get order from buyer, each order will deduct %d connect from seller account.'),$number_of_connect);
        $route = route('seller.subscription.buy');
        $csrf_token = csrf_token();

        // payment gateway
        $payment_gateway = \App\Helpers\PaymentGatewayRenderHelper::renderPaymentGatewayForForm(false, 'old');



         $wallet_gateway = '';
        if (moduleExists('Wallet')) {
            $wallet_gateway = \App\Helpers\PaymentGatewayRenderHelper::renderWalletForm();
        }

        $login_user_type='';
        if(Auth::guard('web')->check()){
            $login_user_type = Auth::guard('web')->user()->user_type == 0 ? 'seller' : '';
        }

        $abc = get_static_option('site_manual_payment_name');
        $abcd = get_static_option('site_manual_payment_description');
        $receipt = __('Receipt');

        $form = <<<FORM
    <div class="form-group">
        <div class="label mt-3 mb-2">$abc  $receipt</div>
        <input type="file" name="manual_payment_image" class="form-control" style="line-height: 3.15">
    </div>
    <div class="manual_description">
       $abcd
    </div>
FORM;


        // price plan Coupon code
        $coupon_placeholder = __('Enter Coupon Code');
        if(!empty(get_static_option('manual_payment_gateway'))){
            $form;
        }
        $price_plan_markup= '';
        $subscriptions = Subscription::where('status',1)->get();

        foreach($subscriptions as $subscription) {
            $s_id = $subscription->id;
            $s_title = $subscription->title;
            $type = $subscription->type;

            // translate
            $subscription_type_text = '';
            if ($type == 'monthly'){
                $subscription_type_text = __('Monthly');
            }elseif($type == 'yearly'){
                $subscription_type_text = __('Yearly');
            }elseif($type == 'lifetime'){
                $subscription_type_text = __('Lifetime');
            }


            $price = float_amount_with_currency_symbol($subscription->price);

            $connect = $type == 'lifetime' ? __('No limit') : $subscription->connect;
            $service = $type == 'lifetime' ? __('No limit') : $subscription->service;
            $job = $type == 'lifetime' ? __('No limit') : $subscription->job;

            $price_without_currency_symbol = $subscription->price;
            $image = render_image_markup_by_attachment_id($subscription->image);
            $month_text = $type; //ucfirst(substr($type,0,2));
            $typeText = $type == 'lifetime' ? __('package user will charge only once') : __('billing cycle, system will deduct this amount from seller account, if seller has balance, otherwise will send an invoice mail to pay the bill');
            $buy_now_markup='';
            if ($type == 'lifetime'){
                $connect_text = __('this package will get unlimited number of connect.');
                $service_text = __('this package will get unlimited number of service.');
                $job_text = __('this package will get unlimited number of job.');
                $if_life_time_style_add = '<li></li>';
            }else{
                $connect_text_line = __('Connect to get order from buyer, each order will deduct');
                $connect_text_line_two = __('connect from seller account');
                $service_text_line = __('Seller can create');
                $service_text_line_two = __('Services Maximum');
                $job_text_line = __('Seller can apply');
                $job_text_line_two = __('Jobs Maximum');

                $connect_text = sprintf(__('%s <strong>%s</strong> %s'),$connect_text_line,$number_of_connect, $connect_text_line_two);
                $service_text = sprintf(__(' %s <strong>%s</strong> %s'),$service_text_line, $service, $service_text_line_two);
                $job_text = sprintf(__(' %s <strong>%s</strong> %s'),$job_text_line, $job, $job_text_line_two);
                $if_life_time_style_add = '';
            }

            if($login_user_type == 'seller'){
                if ($subscription->price == 0){
                    $buy_now_markup.= <<<BUYNOWMARKUP
                <div class="btn-wrapper" xmlns="http://www.w3.org/1999/html">  
                  <form action="{$route}" method="post">
                        <input type="hidden" name="_token" value="{$csrf_token}">
                        <input type="hidden" name="subscription_id" class="subscription_id" value="{$s_id}">
                        <input type="hidden" name="type" class="type" value="{$type}">
                        <input type="hidden" name="price" class="price" value="{$price_without_currency_symbol}">
                        <input type="hidden" name="connect" class="connect" value="{$connect}">            
                         <input type="hidden" name="service" class="service" value="{$service}">
                        <input type="hidden" name="job" class="job" value="{$job}">                   
                         <button type="submit" class="cmn-btn btn-outline-1">{$buy_now_text}</button>
                    </form>                     
                  </div>
                BUYNOWMARKUP;
                }else{
                    $buy_now_markup.=<<<BUYNOWMARKUP
                <div class="btn-wrapper">
                    <a href="#"
                    class="cmn-btn btn-outline-1 get_subscription_id" 
                    data-bs-toggle="modal"  
                    data-bs-target="#buySubscriptionModal"
                    data-id="{$s_id}"
                    data-type="{$type}"
                    data-price="{$price_without_currency_symbol}"
                    data-connect="{$connect}"
                     data-service="{$service}"
                    data-job="{$job}"
                        >{$buy_now_text}</a>
                  </div>
                BUYNOWMARKUP;
                }
            }else{
                $buy_now_markup.=<<<BUYNOWMARKUP
                <div class="btn-wrapper">
                    <span href="#" 
                    class="cmn-btn btn-outline-1 get_subscription_id"
                        style="cursor:no-drop; opacity:0.4">{$buy_now_text}</span>
                  </div>
                BUYNOWMARKUP;
            }

            $service = $subscription->type == 'lifetime' ? __('No limit') : $subscription->service;
            $job = $subscription->type == 'lifetime' ? __('No limit') : $subscription->job;

            if($subscription->type != 'lifetime'){
                $service = '';
                $job ='';
            }

            $price_plan_markup.= <<<PRICEPLAN
               <div class="col-lg-4 col-md-6 mt-5">
                    <div class="pricing-table-10">
                        <div class="icon-area">
                            $image
                            <h3 class="title">{$s_title}</h3>
                        </div>
                        <div class="price-body">
                            <ul>
                                <li><b>{$subscription_type_text}</b> {$typeText}</li>
                                <li><b>{$connect}</b> {$connect_text}</li>                               
                                <li> <b>{$service}</b> {$service_text}</li>                               
                                <li><b>{$job}</b> {$job_text}</li>  
                                {$if_life_time_style_add}
                            </ul>
                        </div>                                            
                        
                        <div class="price-footer">
                            <div class="price">
                                <span class="dollar"></span>{$price}<span class="month">/{$subscription_type_text}</span>
                            </div>
                           {$buy_now_markup}
                        </div>
                    </div>
                </div>
            PRICEPLAN;
        }


// payment option modal new and old
 $payment_option_one_markup='';
    $payment_option_one_markup .= <<<PAYMENTOPTIONONE
         {$wallet_gateway}
            <div class="confirm-payment payment-border">
                <div class="single-checkbox">
                    <div class="checkbox-inlines">
                        <label class="checkbox-label" for="check2">
                            {$payment_gateway}
                        </label>
                    </div>
                </div>
            </div> 
PAYMENTOPTIONONE;



        return <<<HTML

     <!-- About area Starts -->
     <section class="About-area" data-padding-top="{$padding_top}" data-padding-bottom="{$padding_bottom}">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="section-title desktop-center margin-bottom-55">
                        <h2 class="title"> <span style="color:{$title_text_color}"> {$title} </span> </h2>
                        <span class="section-para">{$subtitle}</span>
                    </div>
                </div>
            </div>
            <div class="row">
                  {$price_plan_markup}
            </div>
        </div>
    </section>
    <!-- About area ends -->
    
        <!-- Add Modal -->
    <div class="modal fade" id="buySubscriptionModal" tabindex="-1" role="dialog" aria-labelledby="couponModal" aria-hidden="true">
        <form id="msform" class="ms-order-form" action="{$route}" method="post"  enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{$csrf_token}">
            <input type="hidden" name="subscription_id" class="subscription_id" value="">
            <input type="hidden" name="type" class="type" value="">
            <input type="hidden" name="price" class="price" value="">
            <input type="hidden" name="connect" class="connect" value="">            
             <input type="hidden" name="service" class="service" value="">
            <input type="hidden" name="job" class="job" value="">            
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-warning" id="couponModal">{$subscription_text}</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="confirm-bottom-content">
                                 {$payment_option_one_markup}                            
                            <div class="col-lg-12">
                                <div class="order cart-total">
                                    <div class="form-group">
                                        <input type="hidden" value="" id="subscription_price">
                                        <p class="display_error_msg"></p>
                                        <p class="display_coupon_amount"></p>
                                       <div class="subscription-coupon-btn-group">
                                            <input type="text" name="apply_coupon_code" id="apply_coupon_code" class="form-control mt-2" style="line-height: 3.15" placeholder="{$coupon_placeholder}">
                                            <button type="button" class="btn btn-success coupon_apply_btn mx-4">{$apply}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                        </div>        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{$close_text}</button>
                        <button type="submit" class="btn btn-primary order_create_from_jobs">{$buy_now_text}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>    
HTML;

    }

    public function addon_title()
    {
        return __('Price Plan');
    }
}


