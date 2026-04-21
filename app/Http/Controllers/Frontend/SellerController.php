<?php

namespace App\Http\Controllers\Frontend;

use DB;

use Modules\Wallet\Entities\Wallet;
use Modules\Wallet\Entities\WalletHistory;
use Str;
use Auth;
use App\Day;
use App\Tax;
use App\User;
use App\Admin;
use App\Order;
use App\Report;
use App\Review;
use App\Country;
use App\Service;
use App\Category;
use App\Schedule;
use App\ToDoList;
use Carbon\Carbon;
use App\ServiceArea;
use App\ServiceCity;
use App\Subcategory;
use App\ExtraService;
use App\OrderInclude;
use App\SellerVerify;
use App\StaticOption;
use App\ChildCategory;
use App\PayoutRequest;
use App\ServiceCoupon;
use App\SupportTicket;
use App\AmountSettings;
use App\Mail\BasicMail;
use App\Mail\OrderMail;
use App\Servicebenifit;
use App\Serviceinclude;
use App\Accountdeactive;
use App\AdminCommission;
use App\OrderAdditional;
use App\Helpers\FlashMsg;
use App\OnlineServiceFaq;
use App\AdminNotification;
use App\ReportChatMessage;
use App\Serviceadditional;
use App\EditServiceHistory;
use App\Services\GoogleTranslateService;
use App\Services\SMSService;
use FontLib\Table\Type\post;
use Illuminate\Http\Request;
use App\OrderCompleteDecline;
use App\SupportTicketMessage;
use App\Events\SupportMessage;
use App\OrderBookingDateTimeChange;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Modules\JobPost\Entities\BuyerJob;
use App\Http\Controllers\SMSController;
use App\Notifications\OrderNotification;
use Modules\JobPost\Entities\JobRequest;
use App\Helpers\ServiceCalculationHelper;
use App\Notifications\TicketNotification;


class SellerController extends Controller
{
    public function __construct()
    {
        $this->middleware('inactiveuser');
    }

    public function sellerDashboard()
    {
        $total_earnings = 0;
        $seller_id = Auth::guard('web')->user()->id;
        $pending_order = Order::where(['status' => 0, 'seller_id' => $seller_id])->count();
        $complete_order = Order::where(['status' => 2, 'seller_id' => $seller_id])->count();
        $active_order = Order::where(['status' => 1, 'seller_id' => $seller_id])->count();
        $total_order = Order::where(['seller_id' => $seller_id])->count();

        //balance calculate
        $get_sum = Order::with('extraSevices')->where(['status' => 2, 'seller_id' => $seller_id])->get();
        $total_order_amount = $get_sum->sum('total');
        $extra_service_total = 0;
        $extra_service_total_tax = 0;
        $extra_service_total_commission_amount = 0;

        foreach ($get_sum as $order) {
            if ($order->extraSevices) {
                foreach ($order->extraSevices as $extraService) {
                    if ($extraService->payment_status == 'complete') {
                        $extra_service_total += $extraService->total;
                        $extra_service_total_tax += $extraService->tax;
                        $extra_service_total_commission_amount += $extraService->commission_amount;
                    }
                }
            }
        }

        $complete_order_balance_with_tax = $total_order_amount + $extra_service_total;
        $complete_order_tax = $get_sum->sum('tax') + $extra_service_total_tax;
        $complete_order_balance_without_tax = $complete_order_balance_with_tax - $complete_order_tax;
        $admin_commission_amount = $get_sum->sum('commission_amount') + $extra_service_total_commission_amount;
        $remaning_balance = $complete_order_balance_without_tax - $admin_commission_amount;

        $this_month = Order::where(['seller_id' => $seller_id, 'status' => 2])->whereMonth('created_at', Carbon::now()->month);
        //earning or withdraw calculate
        $total_earnings = PayoutRequest::where('seller_id', $seller_id)->sum('amount');
        $last_five_order = Order::where('seller_id', $seller_id)->latest()->take(4)->get();
        $this_month_order_count = $this_month->count();

        //this month balance calculate        
        $this_month_total_balance_with_tax = $this_month->sum('total');
        $this_month_total_tax = $this_month->sum('tax');
        $this_month_admin_commission = $this_month->sum('commission_amount');
        $this_month_balance_without_tax_and_admin_commission = $this_month_total_balance_with_tax - ($this_month_total_tax + $this_month_admin_commission);
        //this month earning or withdraw calculate
        $this_month_earnings = PayoutRequest::where('seller_id', $seller_id)->whereMonth('created_at', Carbon::now()->month)->sum('amount');

        //to do list 
        $to_do_list = ToDoList::where(['user_id' => $seller_id, 'status' => 0])->take(3)->latest()->get();
        $to_do_list_all = ToDoList::where('user_id', $seller_id)->latest()->get();

        $buyer_count = Order::where('seller_id', $seller_id)->distinct('buyer_id')->count();


        //get last 12 months order
        $month_list = [];
        $monthly_order_list = [];

        for ($i = 0; $i < 12; $i++) {
            $month = Carbon::parse(date('Y') . '-01-01')->addMonth($i);
            $month_list[] = $month->shortMonthName;

            $monthly_order_list[] = Order::where('seller_id', $seller_id)->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at',  $month)
                ->count();
        }

        //get last 7 days order
        $currentDateTime = Carbon::now();
        $days_list = [];
        $pending_order_list = [];
        $active_order_list = [];
        $complete_order_list = [];

        $startWeek = get_static_option("start_week_from");

        for ($i = 0; $i < 7; $i++) {
            $day = $currentDateTime->startOfWeek($startWeek)->addDay($i);
            $days_list[] = $day->dayName;

            $pending_order_list[] = Order::where('seller_id', $seller_id)->where('status', 0)
                ->whereDate('created_at', $day)
                ->count();
            $active_order_list[] = Order::where('seller_id', $seller_id)->where('status', 1)
                ->whereDate('created_at', $day)
                ->count();
            $complete_order_list[] = Order::where('seller_id', $seller_id)->where('status', 2)
                ->whereDate('created_at', $day)
                ->count();
        }

        return view('frontend.user.seller.dashboard.dashboard', compact(
            'pending_order',
            'complete_order',
            'remaning_balance',
            'total_earnings',
            'last_five_order',
            'this_month_order_count',
            'this_month_balance_without_tax_and_admin_commission',
            'this_month_earnings',
            'buyer_count',
            'to_do_list',
            'to_do_list_all',
            'month_list',
            'monthly_order_list',
            'days_list',
            'pending_order_list',
            'active_order_list',
            'complete_order_list',
            'active_order',
            'total_order'
        ));
    }

    // get all days for booking date time change
    public function sellerOrderBookingDaysGet(Request $request)
    {
        try {
            $service_details_for_book = Service::where('id', $request->id)
                ->where(['status' => 1, 'is_service_on' => 1])
                ->firstOrFail();

            $days_count = optional(Day::where('seller_id', $service_details_for_book->seller_id)
                ->select('total_day')
                ->first())
                ->total_day;

            return response()->json([
                'days_count' => $days_count,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve days count.'], 500);
        }
    }


    public function sellerUpdateOrderChangeDate(Request $request)
    {

        $request->validate([
            'order_id' => 'required',
            'service_available_dates' => 'required',
        ]);

        $user = Auth::guard('web')->user();
        if ($user->user_type === 0) {
            $order = Order::where('id', $request->order_id)->where('seller_id', $user->id)->first();
            if (!empty($order)) {
                $order_booking_date_get = OrderBookingDateTimeChange::where('order_id', $request->order_id)->first();
                if (!empty($order_booking_date_get)) {
                    // Update existing record
                    $order_booking_date_get->update([
                        'date' => $request->service_available_dates,
                        'schedule' => $request->service_available_schedule,
                        'status' => 0,
                    ]);
                } else {
                    // Create new record
                    OrderBookingDateTimeChange::create([
                        'order_id' => $request->order_id,
                        'date' => $request->service_available_dates,
                        'schedule' => $request->service_available_schedule,
                        'status' => 0,
                    ]);
                }

                // send order complete request notification seller to buyer
                $buyer = User::where('id', $order->buyer_id)->first();
                if ($buyer) {
                    $order_complete_message = __('You have a new order complete request');
                    $buyer->notify(new OrderNotification($order->id, $order->service_id, $order->seller_id, $order->buyer_id, $order_complete_message));
                }

                toastr_success(__('Order Booking Date & Time  Update Success---'));
            } else {
                toastr_error(__('Order not found'));
            }
        }
        return redirect()->back();
    }

    public function sellerProfile()
    {
        $cities = ServiceCity::where('status', 1)->get();
        $areas = ServiceArea::where('status', 1)->get();
        $countries = Country::where('status', 1)->get();
        $categories = Category::all();
        $user = Auth::guard('web')->user();
        $userCategoryIds = $user->category_ids ?? [];

        return view('frontend.user.seller.profile.seller-profile', compact('countries', 
            'areas', 'cities', 'categories', 'userCategoryIds'));
    }

    public function sellerProfileEdit(Request $request)
    {
        if ($request->isMethod('post')) {
            $user = Auth::guard('web')->user()->id;
            $request->validate([
                'name' => 'required|max:191',
                'email' => 'required|max:191|email|unique:users,email,' . $user,
                'phone' => 'required|max:191',
                'service_area' => 'required|max:191',
                'post_code' => 'required|max:191',
                'address' => 'required|max:191',
                'category_ids' => 'nullable|array',
                'category_ids.*' => 'integer|exists:categories,id',
                'experience' => 'nullable|string|max:191',
            ]);

            $old_image = User::select('image', 'profile_background')->where('id', Auth::guard('web')->user()->id)->first();

            // Check if we should save location data
            $saveLocationData = get_static_option('seller_service_zone_area_save_during_profile_update') === 'on';

            // Prepare the update data
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'image' => $request->image ?? $old_image->image,
                'profile_background' => $request->profile_background ?? $old_image->profile_background,
                'service_city' => $request->service_city,
                'service_area' => $request->service_area,
                'country_id' => $request->country_id,
                'post_code' => $request->post_code,
                'address' => $request->address,
                'about' => $request->about,
                'tax_number' => $request->tax_number,
                'fb_url' => $request->fb_url,
                'tw_url' => $request->tw_url,
                'go_url' => $request->go_url,
                'yo_url' => $request->yo_url,
                'li_url' => $request->li_url,
                'in_url' => $request->in_url,
                'pi_url' => $request->pi_url,
                'dr_url' => $request->dr_url,
                'twi_url' => $request->twi_url,
                're_url' => $request->re_url,
                'category_ids' => $request->category_ids ?? [],
                'experience' => $request->experience,
            ];

            // Only add location data if the setting is enabled
            if ($saveLocationData) {
                // Build the seller address from country, city, and area
                $country = Country::find($request->country_id);
                $city = ServiceCity::find($request->service_city);
                $area = ServiceArea::find($request->service_area);

                $seller_address = '';
                if ($area && !empty($area->service_area)) {
                    $seller_address .= $area->service_area . ', ';
                }
                if ($city && !empty($city->service_city)) {
                    $seller_address .= $city->service_city . ', ';
                }
                if ($country && !empty($country->country)) {
                    $seller_address .= $country->country;
                }

                // Add location data to the update array
                $updateData['latitude'] = $request->latitude;
                $updateData['longitude'] = $request->longitude;
                $updateData['seller_address'] = $seller_address;
            }

            User::where('id', Auth::guard('web')->user()->id)
                ->update($updateData);

            toastr_success(__('Profile Update Success---'));

            $user_info = Auth::guard('web')->user();
            if ($user_info->user_type === 0) {
                Service::where('seller_id', $user_info->id)->update([
                    'service_city_id' => $request->service_city,
                    'service_area_id' => $request->service_area,
                ]);
            }

            return redirect()->back();
        }

        $countries = Country::where('status', 1)->get();
        $user_country = Auth::guard('web')->user()->country_id;
        $cities = ServiceCity::where('country_id', $user_country)->get();
        $areas = ServiceArea::where('service_city_id', Auth::guard('web')->user()->service_city)->get();
        return view('frontend.user.seller.profile.seller-profile-edit', compact('cities', 'areas', 'countries'));
    }

    public function sellerAccountSetting(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'current_password' => 'required|min:6',
                'new_password' => 'required|min:6',
                'confirm_password' => 'required|min:6',
            ]);

            $seller = User::where('id', Auth::user()->id)->first();
            if (Hash::check($request->current_password, $seller->password)) {
                if ($request->new_password == $request->confirm_password) {
                    User::where('id', $seller->id)->update([
                        'password' => Hash::make($request->new_password),
                        'password_changed_at' => now(),
                    ]);
                    toastr_success(__('Password Update Success---'));
                    return redirect()->back();
                }
                toastr_error(__('Password and Confirm Password not match---'));
                return redirect()->back();
            }
            toastr_error(__('Current Password is Wrong---'));
            return redirect()->back();
        }

        $user = Accountdeactive::select('user_id', 'status')->where('user_id', Auth::guard('web')->user()->id)->first();
        return view('frontend.user.seller.profile.seller-account-settings', compact('user'));
    }

    public function accountDeactive(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'reason' => 'required',
                'description' => 'required|max:150',
            ]);

            //first seller order status check
            $auth_seller_id = Auth::guard('web')->user()->id;
            //first seller order status check
            $all_orders = Order::where('seller_id', $auth_seller_id)->where('status', 1)->count();
            if ($all_orders > 1) {
                toastr_error(__('Your have active orders. Please complete them before trying to delete your account.'));
                return redirect()->back();
            } else {
                Accountdeactive::create([
                    'user_id' => Auth::guard('web')->user()->id,
                    'reason' => $request['reason'],
                    'description' => $request['description'],
                    'status' => 0,
                    'account_status' => 0,
                ]);
                Service::where('seller_id', Auth::guard('web')->user()->id)->update(['status' => 0]);

                try {

                    $user_id = Auth::guard('web')->user()->id;
                    $user_name = Auth::guard('web')->user()->name;
                    $user_email = Auth::guard('web')->user()->email;
                    $delete_message = get_static_option('user_permanently_delete_account') ?? __('User delete account for permanently');

                    $title = __('User Account Deletion Request:');
                    $user_id_no = __('User ID:');
                    $user_name_title = __('User Name:');
                    $user_email_title = __('User Email:');
                    $user_req_mas = __('Deletion Request Message:');

                    $message = "<strong>$title</strong><br><br>";
                    $message .= "<strong>$user_id_no</strong> {$user_id}<br>";
                    $message .= "<strong>$user_name_title</strong> {$user_name}<br>";
                    $message .= "<strong>$user_email_title</strong> {$user_email}<br>";
                    $message .= "<strong>$user_req_mas</strong> {$delete_message}<br>";

                    Mail::to(get_static_option('site_global_email'))->queue(new BasicMail([
                        'subject' =>  __('User Account permanently Deletion Request'),
                        'message' => $message
                    ]));
                } catch (\Exception $e) {
                    //
                }

                toastr_error(__('Your Account Successfully Deactivate'));
                return redirect()->back();
            }
        }
    }

    // seller account delete
    public function accountDelete(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'reason' => 'required',
                'description' => 'required|max:150',
            ]);
            $auth_seller_id = Auth::guard('web')->user()->id;
            //first seller order status check
            $all_pending_orders = Order::where('seller_id', $auth_seller_id)->where('status', 0)->count();
            $all_orders = Order::where('seller_id', $auth_seller_id)->where('status', 1)->count();
            if ($all_pending_orders > 1) {
                toastr_error(__('Your have pending orders. Please complete or cancel them before trying to delete your account.'));
                return redirect()->back();
            } elseif ($all_orders > 1) {
                toastr_error(__('Your have active orders. Please complete them before trying to delete your account.'));
                return redirect()->back();
            } else {
                Accountdeactive::create([
                    'user_id' => Auth::guard('web')->user()->id,
                    'reason' => $request['reason'],
                    'description' => $request['description'],
                    'status' => 1,
                    'account_status' => 1,
                ]);
                Service::where('seller_id', Auth::guard('web')->user()->id)
                    ->update(['status' => 0]);

                try {

                    $user_id = Auth::guard('web')->user()->id;
                    $user_name = Auth::guard('web')->user()->name;
                    $user_email = Auth::guard('web')->user()->email;
                    $delete_message = get_static_option('user_permanently_delete_account') ?? __('User delete account for permanently');

                    $title = __('User Account Deletion Request:');
                    $user_id_no = __('User ID:');
                    $user_name_title = __('User Name:');
                    $user_email_title = __('User Email:');
                    $user_req_mas = __('Deletion Request Message:');

                    $message = "<strong>$title</strong><br><br>";
                    $message .= "<strong>$user_id_no</strong> {$user_id}<br>";
                    $message .= "<strong>$user_name_title</strong> {$user_name}<br>";
                    $message .= "<strong>$user_email_title</strong> {$user_email}<br>";
                    $message .= "<strong>$user_req_mas</strong> {$delete_message}<br>";

                    Mail::to(get_static_option('site_global_email'))->queue(new BasicMail([
                        'subject' =>  __('User Account permanently Deletion Request'),
                        'message' => $message
                    ]));
                } catch (\Exception $e) {
                    //
                }

                toastr_error(__('Your Account Delete Successfully'));
            }

            return redirect()->route('seller.logout');
        }
    }

    public function accountDeactiveCancel($id = null)
    {
        $account_details = Accountdeactive::where('user_id', $id)->first();
        $account_details->delete();
        Service::where('seller_id', Auth::guard('web')->user()->id)
            ->update(['status' => 1]);
        toastr_success(__('Your Account Successfully Active'));
        return redirect()->back();
    }

    public function sellerLogout(Request $request)
    {
        Auth::guard('web')->logout();
        return redirect('/');
    }

    //coupons 
    public function serviceCoupon(Request $request)
    {
        if (!empty($request->coupon_code || $request->status || $request->discount_type || $request->coupon_date)) {
            $coupon_query = ServiceCoupon::where('seller_id', Auth::guard('web')->user()->id);

            if (!empty($request->coupon_code)) {
                $coupon_query->where('code', 'LIKE', "%{$request->coupon_code}%");
            }
            if (!empty($request->status)) {
                if ($request->status == 'pending') {
                    $coupon_query->where('status', 0);
                } else {
                    $coupon_query->where('status', $request->status);
                }
            }

            // Discount Type
            if (!empty($request->discount_type)) {
                $coupon_query->where('discount_type', $request->discount_type);
            }

            // search by date range
            if (!empty($request->coupon_date)) {
                $start_date = \Str::of($request->coupon_date)->before('to');
                $end_date = \Str::of($request->coupon_date)->after('to');
                $coupon_query->whereBetween('created_at', [$start_date, $end_date]);
            }
            $coupons = $coupon_query->paginate(10);
        } else {
            $coupons = ServiceCoupon::where('seller_id', Auth::guard('web')->user()->id)->latest()->paginate(10);
        }

        return view('frontend.user.seller.coupons.coupons', compact('coupons'));
    }

    public function addServiceCoupon(Request $request)
    {

        $request->validate([
            'code' => 'required|max:191|unique:service_coupons,code',
            'discount' => 'required|numeric',
            'discount_type' => 'required|max:191',
            'expire_date' => 'required',
        ]);

        ServiceCoupon::create([
            'code' => str_replace(' ', '', $request->code),
            'discount' => $request->discount,
            'discount_type' => $request->discount_type,
            'expire_date' => $request->expire_date,
            'status' => 0,
            'seller_id' => Auth::guard('web')->user()->id,

        ]);

        toastr_success(__('Coupon Added Success---'));
        return redirect()->back();
    }

    public function updateServiceCoupon(Request $request)
    {
        $request->validate([
            'up_code' => 'required|max:191',
            'up_discount' => 'required|numeric',
            'up_discount_type' => 'required|max:191',
            'up_expire_date' => 'required',
        ]);

        ServiceCoupon::where('id', $request->up_id)->update([
            'code' => str_replace(' ', '', $request->up_code),
            'discount' => $request->up_discount,
            'discount_type' => $request->up_discount_type,
            'expire_date' => $request->up_expire_date,
            'seller_id' => Auth::guard('web')->user()->id,
        ]);

        toastr_success(__('Coupon Update Success---'));
        return redirect()->back();
    }

    public function changeCouponStatus($id = null)
    {
        $status = ServiceCoupon::select('status')->where('id', $id)->first();
        if ($status->status == 1) {
            $status = 0;
        } else {
            $status = 1;
        }
        ServiceCoupon::where('id', $id)->update([
            'status' => $status,
        ]);
        toastr_success(__('Coupon status Update Success---'));
        return redirect()->back();
    }

    public function couponDelete($id = null)
    {
        ServiceCoupon::find($id)->delete();
        toastr_error(__('Coupon Delete Success---'));
        return redirect()->back();
    }

    //services
    public function sellerServices(Request $request)
    {


        if (!empty($request->service_id || $request->service_status || $request->service_title || $request->online_offline_status || $request->service_price || $request->service_date)) {

            $services_query = Service::with('reviews', 'pendingOrder', 'completeOrder', 'cancelOrder')->where('seller_id', Auth::user()->id);

            // search by service ID
            if (!empty($request->service_id)) {
                $services_query->where('id', $request->service_id);
            }
            // search by service create date
            if (!empty($request->service_date)) {
                $start_date = \Str::of($request->service_date)->before('to');
                $end_date = \Str::of($request->service_date)->after('to');
                $services_query->whereBetween('created_at', [$start_date, $end_date]);
            }

            // search by service status
            if (!empty($request->service_status)) {
                if ($request->service_status == 'pending') {
                    $services_query->where('status', 0);
                } else {
                    $services_query->where('status', $request->service_status);
                }
            }

            // search by online offline service
            if (!empty($request->online_offline_status)) {
                if ($request->online_offline_status == 'offline') {
                    $services_query->where('is_service_online', 0);
                } else {
                    $services_query->where('is_service_online', $request->online_offline_status);
                }
            }

            // search by service amount
            if (!empty($request->service_price)) {
                $service_id = Service::select('id', 'title')->where('price',  'LIKE', "%{$request->service_price}%")->pluck('id')->toArray();
                $services_query->whereIn('id', $service_id);
            }

            // search by service title
            if (!empty($request->service_title)) {
                $service_id = Service::select('id', 'title')->where('title',  'LIKE', "%{$request->service_title}%")->pluck('id')->toArray();
                $services_query->whereIn('id', $service_id);
            }

            $services = $services_query->latest()->paginate(10);
        } else {
            $services = Service::with('reviews', 'pendingOrder', 'completeOrder', 'cancelOrder')
                ->where('seller_id', Auth::user()->id)
                ->latest()->paginate(10);
        }

        return view('frontend.user.seller.services.services', compact('services'));
    }

    public function addServices(Request $request, GoogleTranslateService $translator)
    {

        $commissionGlobal = AdminCommission::first();
        if (moduleExists('Subscription') && $commissionGlobal->system_type == 'subscription' && empty(auth('web')->user()->subscribedSeller)) {
            toastr_error(__('you must have to subscribe any of our package in order to start selling your services.'));
            return back();
        }

        if ($request->isMethod('post')) {
            // Subscription and verification checks
            $commissionGlobal = AdminCommission::first();
            if (moduleExists('Subscription') && $commissionGlobal->system_type == 'subscription' && empty(auth('web')->user()->subscribedSeller)) {
                toastr_error(__('You must subscribe to a package to start selling services.'));
                return back();
            }

            if (get_static_option('service_create_settings') == 'verified_seller') {
                $seller = SellerVerify::select('seller_id', 'status')->where('seller_id', Auth::guard('web')->user()->id)->first();
                if (!$seller || $seller->status != 1) {
                    toastr_error(__('You must verify your account to add services.'));
                    return redirect()->back();
                }
            }

            if ($commissionGlobal->system_type == 'subscription' && moduleExists('Subscription')) {
                $seller_subscription = \Modules\Subscription\Entities\SellerSubscription::where('seller_id', Auth::guard('web')->user()->id)->first();
                if (is_null($seller_subscription)) {
                    toastr_error(__('You must subscribe to a package to create services.'));
                    return redirect()->back();
                }

                if ($seller_subscription->connect == 0 || $seller_subscription->expire_date <= Carbon::now()) {
                    toastr_error(__('Your subscription is expired.'));
                    return redirect()->back();
                }
            }

            // Validation
            $request->validate([
                'category' => 'required',
                'title' => 'required|max:191|unique:services',
                'description' => 'required|min:150',
                'slug' => 'required',
                'include_service_title.*' => 'nullable|string|max:255',
                'include_service_price.*' => 'nullable|numeric|min:0',
                'additional_service_title.*' => 'nullable|string|max:255',
                'additional_service_price.*' => 'nullable|numeric|min:0',
                'additional_service_image.*' => 'nullable|string',
                'benifits.*' => 'nullable|string|max:255',
                'faqs_title.*' => 'nullable|string|max:255',
                'faqs_description.*' => 'nullable|string',
                'meta_title' => 'nullable|string|max:191',
                'meta_tags' => 'nullable|string',
                'meta_description' => 'nullable|string',
                'facebook_meta_tags' => 'nullable|string',
                'facebook_meta_description' => 'nullable|string',
                'facebook_meta_image' => 'nullable|string',
                'twitter_meta_tags' => 'nullable|string',
                'twitter_meta_description' => 'nullable|string',
                'twitter_meta_image' => 'nullable|string',
                'online_service_price' => 'nullable|numeric|min:0',
                'delivery_days' => 'nullable|integer|min:0',
                'revision' => 'nullable|integer|min:0',
            ]);

            $translatedData = $this->translateFlatServicePayload($translator, $request->all());
            $this->validateTranslatedServiceTitle($translatedData['title'] ?? $request->title);

            // Move dd() here to inspect validated data
            // dd($request->all());

            // Save service
            $seller_country = User::select('id', 'country_id')->where('id', Auth::guard('web')->user()->id)->first();
            $country_tax = Tax::select('tax')->where('country_id', $seller_country->country_id)->first();

            $service_status = get_static_option('service_create_status_settings') == 'approved' ? 1 : 0;

            $service = new Service();
            $service->category_id = $request->category;
            $service->subcategory_id = $request->subcategory;
            $service->child_category_id = $request->child_category;
            $service->title = $translatedData['title'] ?? $request->title;
            $service->title_en = $request->title;
            $service->slug = $this->resolveServiceSlug(
                $request->title,
                $translatedData['title'] ?? $request->title,
                $request->slug
            );
            $service->description = $translatedData['description'] ?? $request->description;
            $service->description_en = $request->description;
            $service->image = $request->input('image', '');
            $service->image_gallery = $request->input('image_gallery', '');
            $service->video = $request->video;
            $service->seller_id = Auth::guard('web')->user()->id;
            $service->service_city_id = Auth::guard('web')->user()->service_city;
            $service->service_area_id = Auth::guard('web')->user()->service_area;
            $service->status = $service_status;
            $service->tax = $country_tax->tax ?? 0;
            $service->is_service_all_cities = $request->is_service_all_cities ?? 0;
            $service->is_service_online = $request->has('is_service_online') ? 1 : 0;
            $service->save();

            // Save include services
            // Calculate service price and update online service fields
            $service_total_price = 0;
            $includeTitles = $translatedData['include_service_title'] ?? $request->input('include_service_title', []);
            $includePrices = $request->input('include_service_price', []);
            $includeQuantities = $request->input('include_service_quantity', []);
            // Save the English originals (before translation) — same pattern as Service Info title_en
            $includeTitlesEn = $request->input('include_service_title', []);
            $allIncludeServices = [];

            if ($request->has('is_service_online')) {
                $service->update([
                    'price' => $request->online_service_price ?? 0,
                    'delivery_days' => $request->delivery_days ?? 0,
                    'revision' => $request->revision ?? 0,
                    'is_service_online' => 1,
                ]);

                foreach ($includeTitles as $key => $title) {
                    if (!empty($title)) {
                        $allIncludeServices[] = [
                            'service_id' => $service->id,
                            'seller_id' => Auth::guard('web')->user()->id,
                            'include_service_title' => $title,
                            'include_service_title_en' => $includeTitlesEn[$key] ?? null,
                            'include_service_price' => 0,
                            'include_service_quantity' => 0,
                        ];
                    }
                }
            } else {
                foreach ($includeTitles as $key => $title) {
                    if (!empty($title)) {
                        $qty = max(1, (int)($includeQuantities[$key] ?? 1));
                        $allIncludeServices[] = [
                            'service_id' => $service->id,
                            'seller_id' => Auth::guard('web')->user()->id,
                            'include_service_title' => $title,
                            'include_service_title_en' => $includeTitlesEn[$key] ?? null,
                            'include_service_price' => $includePrices[$key] ?? 0,
                            'include_service_quantity' => $qty,
                        ];
                        $service_total_price += ($includePrices[$key] ?? 0);
                    }
                }
                $service->update(['price' => $service_total_price]);
            }
            ServiceInclude::insert($allIncludeServices);

            // Save additional services
            $additionalTitles = $translatedData['additional_service_title'] ?? $request->input('additional_service_title', []);
            $additionalPrices = $request->input('additional_service_price', []);
            $additionalImages = $request->input('additional_service_image', []);
            $additionalQuantities = $request->input('additional_service_quantity', []);
            // Save the English originals (before translation) — same pattern as Service Info title_en
            $additionalTitlesEn = $request->input('additional_service_title', []);
            $allAdditionalServices = [];
            foreach ($additionalTitles as $key => $title) {
                if (!empty($title)) {
                    $allAdditionalServices[] = [
                        'service_id' => $service->id,
                        'seller_id' => Auth::guard('web')->user()->id,
                        'additional_service_title' => $title,
                        'additional_service_title_en' => $additionalTitlesEn[$key] ?? null,
                        'additional_service_price' => $additionalPrices[$key] ?? 0,
                        'additional_service_quantity' => max(1, (int)($additionalQuantities[$key] ?? 1)),
                        'additional_service_image' => $additionalImages[$key] ?? null,
                    ];
                }
            }
            Serviceadditional::insert($allAdditionalServices);

            // Save benefits
            $benefits = $translatedData['benifits'] ?? $request->input('benifits', []);
            // Save the English originals (before translation) — same pattern as Service Info title_en
            $benefitsEn = $request->input('benifits', []);
            $allBenefits = [];
            foreach ($benefits as $key => $benefit) {
                if (!empty($benefit)) {
                    $allBenefits[] = [
                        'service_id' => $service->id,
                        'seller_id' => Auth::guard('web')->user()->id,
                        'benifits' => $benefit,
                        'benifits_en' => $benefitsEn[$key] ?? null,
                    ];
                }
            }
            Servicebenifit::insert($allBenefits);

            // Save FAQs
            $faqTitles = $translatedData['faqs_title'] ?? $request->input('faqs_title', []);
            $faqDescriptions = $translatedData['faqs_description'] ?? $request->input('faqs_description', []);
            $allFaqs = [];
            foreach ($faqTitles as $key => $title) {
                if (!empty($title)) {
                    $allFaqs[] = [
                        'service_id' => $service->id,
                        'seller_id' => Auth::guard('web')->user()->id,
                        'title' => $title,
                        'description' => $faqDescriptions[$key] ?? '',
                    ];
                }
            }
            OnlineServiceFaq::insert($allFaqs);

            // Save meta data
            $metaData = [
                'meta_title' => purify_html($translatedData['meta_title'] ?? $request->meta_title),
                'meta_tags' => purify_html($request->meta_tags),
                'meta_description' => purify_html($translatedData['meta_description'] ?? $request->meta_description),
                'facebook_meta_tags' => purify_html($translatedData['facebook_meta_tags'] ?? $request->facebook_meta_tags),
                'facebook_meta_description' => purify_html($translatedData['facebook_meta_description'] ?? $request->facebook_meta_description),
                'facebook_meta_image' => $request->facebook_meta_image,
                'twitter_meta_tags' => purify_html($translatedData['twitter_meta_tags'] ?? $request->twitter_meta_tags),
                'twitter_meta_description' => purify_html($translatedData['twitter_meta_description'] ?? $request->twitter_meta_description),
                'twitter_meta_image' => $request->twitter_meta_image,
            ];

            try {
                if (!empty(array_filter($metaData))) {
                    $service->metaData()->create($metaData);
                }
            } catch (\Exception $e) {
                Log::error('Failed to save service metadata: ' . $e->getMessage());
                toastr_error(__('Failed to save metadata. Please try again.'));
                return redirect()->back();
            }

            // Send approval email
            try {
                $message = get_static_option('service_approve_message');
                $message = str_replace(["@service_id"], [$service->id], $message);
                Mail::to(get_static_option('site_global_email'))->queue(new BasicMail([
                    'subject' => get_static_option('service_approve_subject') ?? __('New Service Approve Request'),
                    'message' => $message
                ]));
            } catch (\Exception $e) {
                \Log::error('Service approval email failed: ' . $e->getMessage());
            }

            toastr_success(__('Service Added Success---'));
            return redirect()->route('seller.services');
        }


        $categories = Category::where('status', 1)->get();
        $sub_categories = Subcategory::all();

        return view('frontend.user.seller.services.add-service', compact('categories', 'sub_categories'));
    }

    public function getSubcategory(Request $request)
    {
        $sub_categories = Subcategory::where('category_id', $request->category_id)->where('status', 1)->get();
        return response()->json([
            'status' => 'success',
            'sub_categories' => $sub_categories,
        ]);
    }

    // child category for service add
    public function getChildCategory(Request $request)
    {
        $child_categories = ChildCategory::where('sub_category_id', $request->sub_cat_id)->where('status', 1)->get();

        return response()->json([
            'status' => 'success',
            'child_category' => $child_categories,
        ]);
    }

    public function serviceAttributes(Request $request)
    {
        $latest_service = Service::where('seller_id', Auth::guard('web')->id())->latest()->first();
        return view('frontend.user.seller.services.service-attributes', compact('latest_service'));
    }

    public function addServiceAttributes(Request $request, GoogleTranslateService $translator)
    {

        $data = $request->all();
        if (isset($data['is_service_online_id'])) {
            if ($data['is_service_online_id'] == 1) {
                $request->validate(
                    [
                        'include_service_title.*' => 'required|max:191',
                        'online_service_price' => 'required|integer',
                        'delivery_days' => 'required|integer',
                        'revision' => 'required|integer',
                        'benifits.*' => 'max:191',
                        'faqs_title.*' => 'max:191',
                        'additional_service_title.*' => 'max:191',
                    ],
                    [
                        'include_service_title.*.required' => __('Title is required'),
                    ]
                );
            }
        } else {
            $request->validate(
                [
                    'include_service_title.*' => 'required|max:191',
                    'include_service_price.*' => 'required|numeric',
                    'include_service_quantity.*' => 'required|numeric',
                    'benifits.*' => 'max:191',
                    'faqs_title.*' => 'max:191',
                    'additional_service_title.*' => 'max:191',
                ],
                [
                    'include_service_title.*.required' => __('Title is required'),
                    'include_service_price.*.required' => __('Price is required'),
                    'include_service_price.*.numeric' => __('Price must be a number'),
                    'include_service_quantity.*.required' => __('Quantity is required'),
                    'include_service_quantity.*.numeric' => __('Quantity must be a number'),
                ]
            );
        }

        $original_data = $data;
        $data = $this->translateFlatServicePayload($translator, $data);

        $all_include_service = [];
        $all_additional_service = [];
        $all_benifits_service = [];
        $online_service_faqs = [];
        $service_total_price = 0;

        if (isset($data['is_service_online_id'])) {
            Service::where('id', $request->service_id)->update([
                'price' => $data['online_service_price'],
                'delivery_days' => $data['delivery_days'],
                'revision' => $data['revision'],
                'is_service_online' => 1,
            ]);

            if ($data['is_service_online_id'] == 1) {
                if (isset($data['include_service_title'])) {
                    foreach ($data['include_service_title'] as $key => $value) {
                        $all_include_service[] = [
                            'service_id' => $request->service_id,
                            'seller_id' => Auth::guard('web')->user()->id,
                            'include_service_title' => $data['include_service_title'][$key],
                            'include_service_title_en' => $original_data['include_service_title'][$key] ?? null,
                            'include_service_price' => 0,
                            'include_service_quantity' => 0,
                        ];
                    }
                }
                Serviceinclude::insert($all_include_service);
            }
        } else {
            if (isset($data['include_service_title'])) {
                foreach ($data['include_service_title'] as $key => $value) {
                    $all_include_service[] = [
                        'service_id' => $request->service_id,
                        'seller_id' => Auth::guard('web')->user()->id,
                        'include_service_title' => $data['include_service_title'][$key],
                        'include_service_title_en' => $original_data['include_service_title'][$key] ?? null,
                        'include_service_price' => $data['include_service_price'][$key],
                        'include_service_quantity' => $data['include_service_quantity'][$key],
                    ];
                    $service_total_price += $data['include_service_price'][$key] * $data['include_service_quantity'][$key];
                }
            }
            Serviceinclude::insert($all_include_service);
            Service::where('id', $request->service_id)->update(['price' => $service_total_price]);
        }

        if (isset($data['additional_service_title'])) {
            foreach ($data['additional_service_title'] as $key => $value) {
                if (!empty($data['additional_service_title'][$key])) {
                    $all_additional_service[] = [
                        'service_id' => $request->service_id,
                        'seller_id' => Auth::guard('web')->user()->id,
                        'additional_service_title' => $data['additional_service_title'][$key],
                        'additional_service_title_en' => $original_data['additional_service_title'][$key] ?? null,
                        'additional_service_price' => $data['additional_service_price'][$key],
                        'additional_service_quantity' => $data['additional_service_quantity'][$key],
                        'additional_service_image' => $data['image'][$key],
                    ];
                }
            }
        }
        Serviceadditional::insert($all_additional_service);

        if (isset($data['benifits'])) {
            foreach ($data['benifits'] as $key => $value) {
                $all_benifits_service[] = [
                    'service_id' => $request->service_id,
                    'seller_id' => Auth::guard('web')->user()->id,
                    'benifits' => $data['benifits'][$key],
                    'benifits_en' => $original_data['benifits'][$key] ?? null,
                ];
            }
        }

        Servicebenifit::insert($all_benifits_service);

        if (isset($data['faqs_title'])) {
            foreach ($data['faqs_title'] as $key => $value) {
                if (!empty($data['faqs_title'][$key])) {
                    $online_service_faqs[] = [
                        'service_id' => $request->service_id,
                        'seller_id' => Auth::guard('web')->user()->id,
                        'title' => $data['faqs_title'][$key],
                        'description' => $data['faqs_description'][$key],
                    ];
                }
            }
        }


        OnlineServiceFaq::insert($online_service_faqs);


        toastr_success(__('Service attributes added success---'));
        return redirect()->route('seller.services');
    }

    public function addServiceAttributesById(Request $request, GoogleTranslateService $translator, $id = null)
    {
        if ($request['is_service_online_id'] == 1) {
            $request->validate(
                [
                    'include_service_title.*' => 'nullable|max:191',
                    'additional_service_title.*' => 'required_with:include_service_title.*|max:191',
                    'benifits.*' => 'max:191',
                    'faqs_title.*' => 'max:191',
                ],
                [
                    'include_service_title.*.required' => __('Title is required'),
                ]
            );
        } else {
            $request->validate(
                [
                    'include_service_title.*' => 'nullable|max:191',
                    'include_service_price.*' => 'required_with:include_service_price.*',
                    'include_service_quantity.*' => 'required_with:include_service_quantity.*',
                    'benifits.*' => 'max:191',
                    'faqs_title.*' => 'max:191',
                    'additional_service_title.*' => 'max:191',
                ],
                [
                    'include_service_title.*.required' => __('Title is required'),
                    'include_service_price.*.required' => __('Price is required'),
                    'include_service_price.*.numeric' => __('Price must be a number'),
                    'include_service_quantity.*.required' => __('Quantity is required'),
                    'include_service_quantity.*.numeric' => __('Quantity must be a number'),
                ]
            );
        }


        $get_service = Service::where('id', $id)->where('seller_id', Auth::guard('web')->user()->id)->first();
        if ($request->isMethod('post')) {
            $data = $request->all();
            $original_data = $data;
            $data = $this->translateFlatServicePayload($translator, $data);

            $all_include_service = [];
            $all_additional_service = [];
            $all_benifits_service = [];
            $online_service_faqs = [];
            $service_total_price = 0;
            $service_total_price_with_new_added_attribute = 0;
            $service_count = 0;

            if (isset($data['is_service_online_id'])) {
                if ($data['is_service_online_id'] == 1) {
                    if (isset($data['include_service_title'])) {
                        foreach ($data['include_service_title'] as $key => $value) {
                            if (!empty($data['include_service_title'][$key])) {
                                $all_include_service[] = [
                                    'service_id' => $request->service_id,
                                    'seller_id' => Auth::guard('web')->user()->id,
                                    'include_service_title' => $data['include_service_title'][$key],
                                    'include_service_title_en' => $original_data['include_service_title'][$key] ?? null,
                                    'include_service_price' => 0,
                                    'include_service_quantity' => 0,
                                ];
                                $service_count++;
                            }
                        }
                    }
                }
            } else {
                if (isset($data['include_service_title'])) {
                    foreach ($data['include_service_title'] as $key => $value) {
                        if (!empty($data['include_service_title'][$key])) {
                            $all_include_service[] = [
                                'service_id' => $request->service_id,
                                'seller_id' => Auth::guard('web')->user()->id,
                                'include_service_title' => $data['include_service_title'][$key],
                                'include_service_title_en' => $original_data['include_service_title'][$key] ?? null,
                                'include_service_price' => (int)$data['include_service_price'][$key],
                                'include_service_quantity' => (int)$data['include_service_quantity'][$key],
                            ];
                            $service_total_price += $data['include_service_price'][$key] * $data['include_service_quantity'][$key];
                            $service_count++;
                        }
                    }
                }
            }

            if ($service_count >= 1) {
                Serviceinclude::insert($all_include_service);
                $service_old_price = Service::where('id', $id)->select('price')->first();
                $service_total_price_with_new_added_attribute = ($service_old_price->price + $service_total_price);
                Service::where('id', $request->service_id)->update(['price' => $service_total_price_with_new_added_attribute]);
            }

            if (isset($data['additional_service_title'])) {
                foreach ($data['additional_service_title'] as $key => $value) {
                    if (!empty($data['additional_service_title'][$key])) {
                        $all_additional_service[] = [
                            'service_id' => $request->service_id,
                            'seller_id' => Auth::guard('web')->user()->id,
                            'additional_service_title' => $data['additional_service_title'][$key],
                            'additional_service_title_en' => $original_data['additional_service_title'][$key] ?? null,
                            'additional_service_price' => $data['additional_service_price'][$key],
                            'additional_service_quantity' => $data['additional_service_quantity'][$key],
                            'additional_service_image' => $data['image'][$key],
                        ];
                        $service_count++;
                    }
                }
            }

            if ($service_count >= 1) {
                Serviceadditional::insert($all_additional_service);
            }

            if (isset($data['benifits'])) {
                foreach ($data['benifits'] as $key => $value) {
                    if (!empty($data['benifits'][$key])) {
                        $all_benifits_service[] = [
                            'service_id' => $request->service_id,
                            'seller_id' => Auth::guard('web')->user()->id,
                            'benifits' => $data['benifits'][$key],
                            'benifits_en' => $original_data['benifits'][$key] ?? null,
                        ];
                        $service_count++;
                    }
                }
            }

            if ($service_count >= 1) {
                Servicebenifit::insert($all_benifits_service);
            }

            if (isset($data['faqs_title'])) {
                foreach ($data['faqs_title'] as $key => $value) {
                    if (!empty($data['faqs_title'][$key])) {
                        $online_service_faqs[] = [
                            'service_id' => $request->service_id,
                            'seller_id' => Auth::guard('web')->user()->id,
                            'title' => $data['faqs_title'][$key],
                            'description' => $data['faqs_description'][$key],
                        ];
                        $service_count++;
                    }
                }
            } else {
            }

            if ($service_count >= 1) {
                OnlineServiceFaq::insert($online_service_faqs);
            }

            if ($service_count <= 0) {
                toastr_error(__('Please input service attributes---'));
                return redirect()->back();
            }

            toastr_success(__('Service attributes added success---'));
            return redirect()->route('seller.services');
        }
        if ($get_service != '') {
            return view('frontend.user.seller.services.add-service-attributes-by-id', compact('get_service'));
        } else {
            abort(404);
        }
    }

    public function ServiceOnOf(Request $request)
    {
        $is_service_on = Service::select('is_service_on')->where('id', $request->service_id)->first();
        if ($is_service_on->is_service_on == 1) {
            $is_service_on = 0;
            Service::where('id', $request->service_id)->update(['is_service_on' => $is_service_on]);
        } else {
            $is_service_on = 1;
            Service::where('id', $request->service_id)->update(['is_service_on' => $is_service_on]);
        }
        return response()->json([
            'status' => 'success',
        ]);
    }

    public function editServices(Request $request, GoogleTranslateService $translator, $id = null)
    {


        $service = Service::findOrFail($id);
        if ($service->seller_id != Auth::guard('web')->user()->id) {
            toastr_error(__('Unauthorized access.'));
            return back();
        }

        if ($request->isMethod('post')) {
            // Subscription and verification checks (same as addServices)
            $commissionGlobal = AdminCommission::first();
            if (moduleExists('Subscription') && $commissionGlobal->system_type == 'subscription' && empty(auth('web')->user()->subscribedSeller)) {
                toastr_error(__('You must subscribe to a package to start selling services.'));
                return back();
            }

            if (get_static_option('service_create_settings') == 'verified_seller') {
                $seller = SellerVerify::select('status')->where('seller_id', Auth::guard('web')->user()->id)->first();
                if (!$seller || $seller->status != 1) {
                    toastr_error(__('You must verify your account to edit services.'));
                    return back();
                }
            }

            if ($commissionGlobal->system_type == 'subscription' && moduleExists('Subscription')) {
                $seller_subscription = \Modules\Subscription\Entities\SellerSubscription::where('seller_id', Auth::guard('web')->user()->id)->first();
                if (is_null($seller_subscription) || $seller_subscription->connect == 0 || $seller_subscription->expire_date <= Carbon::now()) {
                    toastr_error(__('Your subscription is expired.'));
                    return back();
                }
            }

            // Validation
            $request->validate([
                'category' => 'required',
                'title' => 'required|max:191|unique:services,title,' . $id,
                'description' => 'required|min:150',
                'slug' => 'required',
                'include_service_inputs.*.include_service_title' => 'nullable|string|max:255',
                'include_service_inputs.*.include_service_price' => 'nullable|numeric|min:0',
                'additional_service_inputs.*.additional_service_title' => 'nullable|string|max:255',
                'additional_service_inputs.*.additional_service_price' => 'nullable|numeric|min:0',
                'additional_service_inputs.*.additional_service_image' => 'nullable|string',
                'service_benefit_inputs.*.benifits' => 'nullable|string|max:255',
                'online_service_faq.*.title' => 'nullable|string|max:255',
                'online_service_faq.*.description' => 'nullable|string',
                'meta_title' => 'nullable|string|max:191',
                'meta_tags' => 'nullable|string',
                'meta_description' => 'nullable|string',
                'facebook_meta_tags' => 'nullable|string',
                'facebook_meta_description' => 'nullable|string',
                'facebook_meta_image' => 'nullable|string',
                'twitter_meta_tags' => 'nullable|string',
                'twitter_meta_description' => 'nullable|string',
                'twitter_meta_image' => 'nullable|string',
                'service_total_price' => 'nullable|numeric|min:0',
                'delivery_days' => 'nullable|integer|min:0',
                'revisions' => 'nullable|integer|min:0',
                'is_service_all_cities' => 'nullable|in:0,1',
                'is_service_online' => 'required|in:0,1',
                'video' => 'nullable|string',
                'image' => 'nullable|string',
                'image_gallery' => 'nullable|string',
            ]);

            // Capture original English inputs BEFORE translation (same pattern as Service Info title_en)
            $originalInputs = $request->all();
            $translatedData = $this->translateNestedServicePayload($translator, $request->all());
            $this->validateTranslatedServiceTitle($translatedData['title'] ?? $request->title, $id);

            // Fix seller country query
            $seller_country = User::select('country_id')->where('id', Auth::guard('web')->user()->id)->first();
            $country_tax = Tax::select('tax')->where('country_id', $seller_country->country_id)->first();

            // Determine service status
            $service_status = get_static_option('service_create_status_settings') == 'approved' ? 1 : 0;

            // Begin transaction for all updates
            DB::beginTransaction();
            try {
                // Update service
                $service->update([
                    'category_id' => $request->category,
                    'subcategory_id' => $request->subcategory,
                    'child_category_id' => $request->child_category,
                    'title' => $translatedData['title'] ?? $request->title,
                    'title_en' => $request->title,
                    'slug' => $this->resolveServiceSlug(
                        $request->title,
                        $translatedData['title'] ?? $request->title,
                        $request->slug
                    ),
                    'description' => $translatedData['description'] ?? $request->description,
                    'description_en' => $request->description,
                    'image' => $request->image ?? $service->image,
                    'image_gallery' => $request->image_gallery ?? $service->image_gallery,
                    'video' => $request->video,
                    'service_city_id' => Auth::guard('web')->user()->service_city,
                    'service_area_id' => Auth::guard('web')->user()->service_area,
                    'tax' => $country_tax->tax ?? 0,
                    'status' => $service_status,
                    'is_service_all_cities' => $request->has('is_service_all_cities') ? $request->is_service_all_cities : $service->is_service_all_cities,
                    'is_service_online' => $request->is_service_online,
                    'delivery_days' => $request->has('is_service_online') ? ($request->delivery_days ?? 0) : 0,
                    'revision' => $request->has('is_service_online') ? ($request->revisions ?? 0) : 0,
                ]);

                // Update Included Services
                ServiceInclude::where('service_id', $service->id)->delete();
                $service_total_price = 0;
                $allIncludeServices = [];
                $originalIncludes = $originalInputs['include_service_inputs'] ?? [];
                foreach (($translatedData['include_service_inputs'] ?? $request->input('include_service_inputs', [])) as $key => $input) {
                    if (!empty($input['include_service_title'])) {
                        $allIncludeServices[] = [
                            'service_id' => $service->id,
                            'seller_id' => Auth::guard('web')->user()->id,
                            'include_service_title' => $input['include_service_title'],
                            // EN = original English before translation
                            'include_service_title_en' => $originalIncludes[$key]['include_service_title'] ?? $input['include_service_title'],
                            'include_service_price' => $request->is_service_online == 1 ? 0 : ($input['include_service_price'] ?? 0),
                            'include_service_quantity' => $request->is_service_online == 1 ? 0 : (int)($input['include_service_quantity'] ?? 1),
                        ];
                        if ($request->is_service_online != 1) {
                            $service_total_price += ($input['include_service_price'] ?? 0) * (int)($input['include_service_quantity'] ?? 1);
                        }
                    }
                }
                if (!empty($allIncludeServices)) {
                    ServiceInclude::insert($allIncludeServices);
                }
                if ($request->is_service_online == 1) {
                    $service->update([
                        'price' => $request->price ?? 0
                    ]);
                } else {
                    $service->update([
                        'price' => $service_total_price
                    ]);
                }

                // Update Additional Services
                Serviceadditional::where('service_id', $service->id)->delete();
                $allAdditionalServices = [];
                $originalAdditional = $originalInputs['additional_service_inputs'] ?? [];
                foreach (($translatedData['additional_service_inputs'] ?? $request->input('additional_service_inputs', [])) as $key => $input) {
                    if (!empty($input['additional_service_title'])) {
                        $allAdditionalServices[] = [
                            'service_id' => $service->id,
                            'seller_id' => Auth::guard('web')->user()->id,
                            'additional_service_title' => $input['additional_service_title'],
                            // EN = original English before translation
                            'additional_service_title_en' => $originalAdditional[$key]['additional_service_title'] ?? $input['additional_service_title'],
                            'additional_service_price' => $input['additional_service_price'] ?? 0,
                            'additional_service_quantity' => (int)($input['additional_service_quantity'] ?? 1),
                            'additional_service_image' => $input['additional_service_image'] ?? null,
                        ];
                    }
                }
                if (!empty($allAdditionalServices)) {
                    Serviceadditional::insert($allAdditionalServices);
                }

                // Update Benefits
                Servicebenifit::where('service_id', $service->id)->delete();
                $allBenefits = [];
                $originalBenefits = $originalInputs['service_benefit_inputs'] ?? [];
                foreach (($translatedData['service_benefit_inputs'] ?? $request->input('service_benefit_inputs', [])) as $key => $input) {
                    if (!empty($input['benifits'])) {
                        $allBenefits[] = [
                            'service_id' => $service->id,
                            'seller_id' => Auth::guard('web')->user()->id,
                            'benifits' => $input['benifits'],
                            // EN = original English before translation
                            'benifits_en' => $originalBenefits[$key]['benifits'] ?? $input['benifits'],
                        ];
                    }
                }
                if (!empty($allBenefits)) {
                    Servicebenifit::insert($allBenefits);
                }

                // Update FAQs
                OnlineServiceFaq::where('service_id', $service->id)->delete();
                $allFaqs = [];
                foreach (($translatedData['online_service_faq'] ?? $request->input('online_service_faq', [])) as $input) {
                    if (!empty($input['title'])) {
                        $allFaqs[] = [
                            'service_id' => $service->id,
                            'seller_id' => Auth::guard('web')->user()->id,
                            'title' => $input['title'],
                            'description' => $input['description'] ?? '',
                        ];
                    }
                }
                if (!empty($allFaqs)) {
                    OnlineServiceFaq::insert($allFaqs);
                }

                // Update Meta Data
                $metaData = [
                    'meta_title' => purify_html($translatedData['meta_title'] ?? $request->meta_title),
                    'meta_tags' => purify_html($request->meta_tags),
                    'meta_description' => purify_html($translatedData['meta_description'] ?? $request->meta_description),
                    'facebook_meta_tags' => purify_html($translatedData['facebook_meta_tags'] ?? $request->facebook_meta_tags),
                    'facebook_meta_description' => purify_html($translatedData['facebook_meta_description'] ?? $request->facebook_meta_description),
                    'facebook_meta_image' => $request->facebook_meta_image,
                    'twitter_meta_tags' => purify_html($translatedData['twitter_meta_tags'] ?? $request->twitter_meta_tags),
                    'twitter_meta_description' => purify_html($translatedData['twitter_meta_description'] ?? $request->twitter_meta_description),
                    'twitter_meta_image' => $request->twitter_meta_image,
                ];
                $service->metaData()->updateOrCreate(
                    [
                        'meta_taggable_id' => $service->id,
                        'meta_taggable_type' => get_class($service)
                    ],
                    array_filter($metaData)
                );

                // Save Edit History
                EditServiceHistory::create([
                    'service_id' => $id,
                    'seller_id' => Auth::guard('web')->user()->id,
                    'service_title' => $translatedData['title'] ?? $request->title,
                    'service_description' => $translatedData['description'] ?? $request->description,
                ]);

                // Send approval email (if re-approval needed)
                if ($service_status == 0) {
                    try {
                        $message = get_static_option('service_approve_message');
                        $message = str_replace(["@service_id"], [$service->id], $message);
                        Mail::to(get_static_option('site_global_email'))->queue(new BasicMail([
                            'subject' => get_static_option('service_approve_subject') ?? __('Service Approval Request'),
                            'message' => $message
                        ]));
                    } catch (\Exception $e) {
                        \Log::error('Service approval email failed: ' . $e->getMessage());
                    }
                }

                DB::commit();
                toastr_success(__('Service updated successfully'));
                return redirect()->route('seller.services');
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Service update failed: ' . $e->getMessage());
                toastr_error(__('Failed to update service. Please try again.'));
                return back();
            }
        }

        $service = Service::with('subcategory', 'childcategory')->find($id);

        if ($service != '') {
            $edit_service_id = $id;
            return view('frontend.user.seller.services.edit-service', compact('edit_service_id'));
        } else {
            abort(404);
        }
    }


    public function editServiceAttribute(Request $request, GoogleTranslateService $translator, $id = null)
    {
        // update
        if ($request->isMethod('post')) {
            $data = $request->all();
            if (isset($data['is_service_online_id'])) {

                if ($data['is_service_online_id'] == 1) {
                    $request->validate(
                        [
                            'include_service_title.*' => 'required|max:191',
                            'online_service_price' => 'required|integer',
                            'delivery_days' => 'required|integer',
                            'revision' => 'required|integer',
                            'benifits.*' => 'max:191',
                            'faqs_title.*' => 'max:191',
                            'additional_service_title.*' => 'max:191',
                        ],
                        [
                            'include_service_title.*.required' => __('Title is required'),
                        ]
                    );
                }
            } else {
                $request->validate(
                    [
                        'include_service_title.*' => 'required|max:191',
                        'include_service_price.*' => 'required|numeric',
                        'include_service_quantity.*' => 'required|numeric',
                        'benifits.*' => 'max:191',
                        'faqs_title.*' => 'max:191',
                        'additional_service_title.*' => 'max:191',
                    ],
                    [
                        'include_service_title.*.required' => __('Title is required'),
                        'include_service_price.*.required' => __('Price is required'),
                        'include_service_price.*.numeric' => __('Price must be a number'),
                        'include_service_quantity.*.required' => __('Quantity is required'),
                        'include_service_quantity.*.numeric' => __('Quantity must be a number'),
                    ]
                );
            }

            $data = $this->translateFlatServicePayload($translator, $data);

            $all_include_service = [];
            $all_additional_service = [];
            $all_benifits_service = [];
            $service_total_price = 0;

            $x = [
                'include' => [],
            ];

            if (isset($data['is_service_online_id'])) {
                if ($data['is_service_online_id'] == 1) {
                    Service::where('id', $id)->update([
                        'price' => $data['online_service_price'],
                        'delivery_days' => $data['delivery_days'],
                        'revision' => $data['revision'],
                    ]);
                    if (isset($data['include_service_title'])) {
                        foreach ($data['include_service_title'] as $key => $value) {
                            Serviceinclude::where('id', $data['service_include_id'][$key])->update([
                                'include_service_title' => $data['include_service_title'][$key],
                                'include_service_price' => 0,
                                'include_service_quantity' => 0,
                            ]);
                        }
                    }
                }
            } else {
                if (isset($data['include_service_title'])) {
                    foreach ($data['include_service_title'] as $key => $value) {
                        Serviceinclude::where('id', $data['service_include_id'][$key])->update([
                            'include_service_title' => $data['include_service_title'][$key],
                            'include_service_price' => $data['include_service_price'][$key],
                            'include_service_quantity' => $data['include_service_quantity'][$key],
                        ]);
                        $service_total_price += $data['include_service_price'][$key] * $data['include_service_quantity'][$key];
                    }
                    Service::where('id', $id)->update(['price' => $service_total_price]);
                }
            }

            if (isset($data['additional_service_title'])) {
                foreach ($data['additional_service_title'] as $key => $value) {
                    $old_image = Serviceadditional::select('additional_service_image')->where('id', $data['service_additional_id'][$key])->first();

                    Serviceadditional::where('id', $data['service_additional_id'][$key])->update([
                        'additional_service_title' => $data['additional_service_title'][$key],
                        'additional_service_price' => $data['additional_service_price'][$key],
                        'additional_service_quantity' => $data['additional_service_quantity'][$key],
                        'additional_service_image' => $data['image'][$key],
                        'additional_service_image' => $data['image'][$key] ?? $old_image->additional_service_image,
                    ]);
                }
            }

            if (isset($data['benifits'])) {
                foreach ($data['benifits'] as $key => $value) {
                    Servicebenifit::where('id', $data['service_benifit_id'][$key])->update([
                        'benifits' => $data['benifits'][$key],
                    ]);
                }
            }

            if (isset($data['faqs_title'])) {
                foreach ($data['faqs_title'] as $key => $value) {
                    OnlineServiceFaq::where('id', $data['online_service_faq_id'][$key])->update([
                        'title' => $data['faqs_title'][$key],
                        'description' => $data['faqs_description'][$key],
                    ]);
                }
            }

            toastr_success(__('Service Attributes Updated Success---'));
            return redirect()->route('seller.services');
        }

        $service = Service::find($id);
        if ($service != '') {
            $service_includes = ServiceInclude::where('service_id', $id)->get();
            $service_additionals = ServiceAdditional::where('service_id', $id)->get();
            $service_benifits = ServiceBenifit::where('service_id', $id)->get();
            $online_service_faq = OnlineServiceFaq::where('service_id', $id)->get();

            return view('frontend.user.seller.services.edit-service-attributes', compact(
                'service',
                'service_includes',
                'service_additionals',
                'service_benifits',
                'online_service_faq',
            ));
        } else {
            abort(404);
        }
    }

    // service online to offline and offline to online
    public function editServiceAttributeOfflineToOnline(Request $request, GoogleTranslateService $translator, $id = null)
    {
        $get_service = Service::where('id', $id)->where('seller_id', Auth::guard('web')->user()->id)->first();
        if ($request->isMethod('post')) {
            $data = $request->all();
            $data = $this->translateFlatServicePayload($translator, $data);

            $all_include_service = [];
            $all_additional_service = [];
            $all_benifits_service = [];
            $online_service_faqs = [];
            $service_total_price = 0;
            $service_total_price_with_new_added_attribute = 0;
            $service_count = 0;

            if (isset($data['is_service_online_id'])) {
                if ($data['is_service_online_id'] == 1) {
                    $this->validate($request, [
                        'online_service_price' => 'required',
                        'delivery_days' => 'required',
                        'benifits.*' => 'max:191',
                        'faqs_title.*' => 'max:191',
                        'additional_service_title.*' => 'max:191',
                        'include_service_title.*' => 'max:191',
                    ]);

                    Serviceinclude::where('service_id', $id)->delete();
                    Serviceadditional::where('service_id', $id)->delete();
                    Servicebenifit::where('service_id', $id)->delete();

                    Service::where('id', $id)->update([
                        'price' => $data['online_service_price'],
                        'delivery_days' => $data['delivery_days'],
                        'revision' => $data['revision'],
                    ]);

                    if (isset($data['include_service_title'])) {
                        foreach ($data['include_service_title'] as $key => $value) {
                            if (!empty($data['include_service_title'][$key])) {
                                $all_include_service[] = [
                                    'service_id' => $request->service_id,
                                    'seller_id' => Auth::guard('web')->user()->id,
                                    'include_service_title' => $data['include_service_title'][$key],
                                    'include_service_price' => 0,
                                    'include_service_quantity' => 0,
                                ];
                                $service_count++;
                            }
                        }
                    }
                }
            }

            if ($data['is_service_online_id'] == 0) {

                Serviceinclude::where('service_id', $id)->delete();
                Serviceadditional::where('service_id', $id)->delete();
                Servicebenifit::where('service_id', $id)->delete();

                foreach ($data['include_service_title'] as $key => $value) {
                    if (!empty($data['include_service_title'][$key])) {
                        $all_include_service[] = [
                            'service_id' => $request->service_id,
                            'seller_id' => Auth::guard('web')->user()->id,
                            'include_service_title' => $data['include_service_title'][$key],
                            'include_service_price' => $data['include_service_price'][$key],
                            'include_service_quantity' => $data['include_service_quantity'][$key],
                        ];
                        $service_total_price += $data['include_service_price'][$key] * $data['include_service_quantity'][$key];
                        $service_count++;
                    }
                }
            }

            if ($data['is_service_online_id'] == 0) {
                Serviceinclude::insert($all_include_service);
                $service_old_price = Service::where('id', $id)->select('price')->first();
                $service_total_price_with_new_added_attribute = $service_total_price;
                Service::where('id', $request->service_id)->update(['price' => $service_total_price_with_new_added_attribute]);
            }

            if (isset($data['additional_service_title'])) {
                foreach ($data['additional_service_title'] as $key => $value) {
                    if (!empty($data['additional_service_title'][$key])) {
                        $all_additional_service[] = [
                            'service_id' => $request->service_id,
                            'seller_id' => Auth::guard('web')->user()->id,
                            'additional_service_title' => $data['additional_service_title'][$key],
                            'additional_service_price' => $data['additional_service_price'][$key],
                            'additional_service_quantity' => $data['additional_service_quantity'][$key],
                            'additional_service_image' => $data['image'][$key],
                        ];
                        $service_count++;
                    }
                }
            }

            if ($service_count >= 1) {
                Serviceadditional::insert($all_additional_service);
            }

            if (isset($data['benifits'])) {
                foreach ($data['benifits'] as $key => $value) {
                    if (!empty($data['benifits'][$key])) {
                        $all_benifits_service[] = [
                            'service_id' => $request->service_id,
                            'seller_id' => Auth::guard('web')->user()->id,
                            'benifits' => $data['benifits'][$key],
                        ];
                        $service_count++;
                    }
                }
            }

            if ($service_count >= 1) {
                Servicebenifit::insert($all_benifits_service);
            }

            if (isset($data['faqs_title'])) {
                foreach ($data['faqs_title'] as $key => $value) {
                    if (!empty($data['faqs_title'][$key])) {
                        $online_service_faqs[] = [
                            'service_id' => $request->service_id,
                            'seller_id' => Auth::guard('web')->user()->id,
                            'title' => $data['faqs_title'][$key],
                            'description' => $data['faqs_description'][$key],
                        ];
                        $service_count++;
                    }
                }
            }
            if ($service_count >= 1) {
                OnlineServiceFaq::insert($online_service_faqs);
            }

            // update offline to online service is_service_online value 0 to change 1 6060
            if ($data['is_service_online_id'] == 1) {
                Service::where('id', $id)->update([
                    'is_service_online' => 1,
                ]);
            }

            //update online to offline service is_service_online value 1 to change 0
            if ($data['is_service_online_id'] == 0) {
                OnlineServiceFaq::where('service_id', $id)->delete();
                Service::where('id', $id)->update([
                    'is_service_online' => 0,
                    'delivery_days' => 0,
                    'revision' => 0,
                    'online_service_price' => 0,
                ]);
            }

            if ($service_count <= 0) {
                toastr_error(__('Please input service attributes---'));
                return redirect()->back();
            }

            toastr_success(__('Service Edit attributes added success---'));

            return redirect()->route('seller.edit.service.attribute', $id);
        }
        if ($get_service != '') {
            return view('frontend.user.seller.services.add-service-attributes-offline-to-online-by-id', compact('get_service'));
        } else {
            abort(404);
        }
    }

    public function ServiceDelete($id = null)
    {
        Serviceinclude::where('service_id', $id)->delete();
        Serviceadditional::where('service_id', $id)->delete();
        Servicebenifit::where('service_id', $id)->delete();
        OnlineServiceFaq::where('service_id', $id)->delete();
        Service::find($id)->delete();
        toastr_error(__('Service Delete Success---'));
        return redirect()->back();
    }

    public function showServiceAttributesById($id = null)
    {
        $seller_id = Auth::guard('web')->user()->id;
        $service = Service::select('id', 'title', 'image')
            ->where('id', $id)
            ->where('seller_id', $seller_id)
            ->first();

        if (!empty($service)) {
            $include_service = Serviceinclude::where('service_id', $id)->get();
            $additional_service = Serviceadditional::where('service_id', $id)->get();
            $service_benifit = Servicebenifit::where('service_id', $id)->get();
            $service_faqs = OnlineServiceFaq::where('service_id', $id)->get();
            return view('frontend.user.seller.services.show-service-attributes-by-id', compact('service', 'include_service', 'additional_service', 'service_benifit', 'service_faqs'));
        }
        abort(404);
    }

    public function deleteIncludeService($id = null)
    {
        $include_details = Serviceinclude::find($id);

        //todo udpate service price
        $service_details = Service::where('id', $include_details->service_id)->first();
        $service_details->price -= $include_details->include_service_price * $include_details->include_service_quantity;
        $service_details->save();

        $include_details->delete();


        toastr_error(__('Include Service Delete Success---'));
        return redirect()->back();
    }

    public function deleteAdditionalService($id = null)
    {
        Serviceadditional::find($id)->delete();
        toastr_error(__('Additional Service Delete Success---'));
        return redirect()->back();
    }

    public function deleteBenifit($id = null)
    {
        Servicebenifit::find($id)->delete();
        toastr_error(__('Service Benifit Delete Success---'));
        return redirect()->back();
    }

    public function deleteFaq($id = null)
    {
        OnlineServiceFaq::find($id)->delete();
        toastr_error(__('Service Faq Delete Success---'));
        return redirect()->back();
    }

    //dates 
    public function days()
    {
        $days = Day::with('schedules')->where('seller_id', Auth::guard('web')->user()->id)->get();
        $total_day = Day::select('total_day')->where('seller_id', Auth::guard('web')->user()->id)->first();
        return view('frontend.user.seller.day-and-schedule.days', compact('days', 'total_day'));
    }

    public function addDay(Request $request)
    {
        $request->validate([
            'day' => 'required',
        ]);

        $day = Day::select('day', 'seller_id')
            ->where('seller_id', Auth::guard('web')->user()->id)
            ->where('day', $request->day)
            ->first();
        if (!empty($day)) {
            toastr_error(__('Day Already Exists---'));
            return redirect()->back();
        }

        Day::create([
            'day' => $request->day,
            'status' => 0,
            'seller_id' => Auth::guard('web')->user()->id,
            'total_day' => 7,
        ]);

        toastr_success(__('Day Added Success---'));
        return redirect()->back();
    }

    public function dayDelete($id = null)
    {
        Schedule::where('day_id', $id)->delete();
        Day::find($id)->delete();
        toastr_error(__('Day Delete Success---'));
        return redirect()->back();
    }

    public function updateTotalDay(Request $request)
    {
        Day::where('seller_id', Auth::guard('web')->user()->id)
            ->update(['total_day' => $request->total_day]);
        toastr_success(__('Service Day Update Success---'));
        return redirect()->back();
    }

    //schedules
    public function schedules()
    {
        $schedules = Schedule::with('days')->where('seller_id', Auth::guard('web')->user()->id)->paginate(10);
        $days = Day::where('seller_id', Auth::guard('web')->user()->id)->get();
        //todo: insert days programmatically if no days available
        $days_lists = $days->pluck('day')->toArray();
        $days_need_to_add = ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
        if (empty($days_lists)) {
            foreach ($days_need_to_add as $dlit) {
                if (!in_array($dlit, $days_lists)) {
                    Day::create([
                        'day' => $dlit,
                        'status' => 0,
                        'seller_id' => Auth::guard('web')->user()->id,
                        'total_day' => 7,
                    ]);
                }
            }
        }

        return view('frontend.user.seller.day-and-schedule.schedules', compact('schedules', 'days'));
    }

    public function addSchedule(Request $request)
    {
        $rule = $request->has('schedule_for_all_days') ? 'nullable' : 'required';
        $request->validate([
            'day_id' => $rule . '|integer',
            'schedule' => 'required',
        ]);
        if ($request->has('schedule_for_all_days')) {
            $days = Day::where('seller_id', Auth::guard('web')->user()->id)->get();
            foreach ($days as $day) {
                Schedule::create([
                    'day_id' => $day->id,
                    'seller_id' => Auth::guard('web')->user()->id,
                    'schedule' => $request->schedule,
                    'status' => 0,
                    'allow_multiple_schedule' => 'no',
                ]);
            }
            toastr_success(__('Schedule Added Success---'));
            return redirect()->back();
        }
        Schedule::create([
            'day_id' => $request->day_id,
            'seller_id' => Auth::guard('web')->user()->id,
            'schedule' => $request->schedule,
            'status' => 0,
            'allow_multiple_schedule' => 'no',
        ]);

        toastr_success(__('Schedule Added Success---'));
        return redirect()->back();
    }

    public function editSchedule(Request $request)
    {
        $request->validate([
            'up_day_id' => 'required',
            'up_schedule' => 'required',
        ]);

        Schedule::where('id', $request->up_id)->update([
            'day_id' => $request->up_day_id,
            'seller_id' => Auth::guard('web')->user()->id,
            'schedule' => $request->up_schedule,
        ]);

        toastr_success(__('Schedule Update Success---'));
        return redirect()->back();
    }

    public function scheduleDelete($id = null)
    {
        Schedule::find($id)->delete();
        toastr_error(__('Schedule Delete Success---'));
        return redirect()->back();
    }

    public function allow(Request $request)
    {
        Schedule::where('seller_id', Auth::guard('web')->user()->id)->update([
            'allow_multiple_schedule' => $request->allow_multiple_schedule,
        ]);
        toastr_success(__('Update Success---'));
        return back();
    }

    //orders
    public function pendingOrders(Request $request)
    {
        if (!empty($request->order_id || $request->order_date)) {
            $order_query = Order::with('service')->where('seller_id', Auth::guard('web')->user()->id)->where('status', 0);

            if (!empty($request->order_id)) {
                $order_query->where('id', $request->order_id);
            }

            // search by date range
            if (!empty($request->order_date)) {
                $start_date = \Str::of($request->order_date)->before('to');
                $end_date = \Str::of($request->order_date)->after('to');
                $order_query->whereBetween('created_at', [$start_date, $end_date]);
            }
            $pending_orders = $order_query->paginate(10);
        } else {
            $pending_orders = Order::with('service')
                ->where('seller_id', Auth::guard('web')->user()->id)
                ->where('status', 0)
                ->paginate(10);
        }

        return view('frontend.user.seller.order.pending-orders', compact('pending_orders'));
    }

    public function orderDelete($id = null)
    {
        $order = Order::find($id);
        if ($order->payment_status == 'pending' || $order->payment_status == '') {
            Order::find($id)->delete();
            toastr_error(__('Order Delete Success---'));
        } else {
            toastr_error(__('Order Can Not be Deleted Due to Payment Status Complete---'));
        }
        return redirect()->back();
    }

    public function sellerOrders(Request $request, $status = null)
{
    $orders_query = Order::with('online_order_ticket', 'order_date_change_request')
        ->where('seller_id', Auth::guard('web')->user()->id)
        ->where('job_post_id', NULL);

    // Apply status from URL param if no order_status filter in request
    if ($status !== null && !$request->has('order_status')) {
        $orders_query->where('status', $status);
    }

    // Check if any filter in request (fix: use !empty() on each individually)
    if (!empty($request->order_id) || !empty($request->order_date) || !empty($request->payment_status) || 
        !empty($request->order_status) || !empty($request->total) || !empty($request->seller_name) || 
        !empty($request->service_title) || !empty($request->buyer_name)) {

        if (!empty($request->order_id)) {
            $orders_query->where('id', $request->order_id);
        }
        if (!empty($request->order_date)) {
            $start_date = \Str::of($request->order_date)->before('to');
            $end_date = \Str::of($request->order_date)->after('to');
            $orders_query->whereBetween('created_at', [$start_date, $end_date]);
        }
        if (!empty($request->payment_status)) {
            $orders_query->where('payment_status', $request->payment_status);
        }
        if (!empty($request->order_status)) {
            if ($request->order_status == 'pending') {
                $orders_query->where('status', 0);
            } else {
                $orders_query->where('status', $request->order_status);
            }
        }
        if (!empty($request->total)) {
            $orders_query->where('payment_status', $request->total);
        }
        if (!empty($request->service_title)) {
            $service_ids = Service::where('title', 'LIKE', "%{$request->service_title}%")->pluck('id');
            $orders_query->whereIn('service_id', $service_ids);
        }
        if (!empty($request->buyer_name)) {
            $buyer_ids = User::where('name', 'LIKE', "%{$request->buyer_name}%")->pluck('id');
            $orders_query->whereIn('buyer_id', $buyer_ids);
        }
    }

    $all_orders = $orders_query->latest()->paginate(10);
    
    $orders = Order::where('seller_id', Auth::guard('web')->user()->id)
        ->where('job_post_id', NULL)->get();

    $pending_orders = Order::where('seller_id', Auth::guard('web')->user()->id)
        ->where('job_post_id', NULL)->where('status', 0)->get();

    $active_orders = Order::where('seller_id', Auth::guard('web')->user()->id)
        ->where('job_post_id', NULL)->where('status', 1)->get();

    $complete_orders = Order::where('seller_id', Auth::guard('web')->user()->id)
        ->where('job_post_id', NULL)->where('status', 2)->get();

    $deliver_orders = Order::where('seller_id', Auth::guard('web')->user()->id)
        ->where('job_post_id', NULL)->where('status', 3)->get();

    $cancel_orders = Order::where('seller_id', Auth::guard('web')->user()->id)
        ->where('job_post_id', NULL)->where('status', 4)->get();

    return view('frontend.user.seller.order.orders', compact(
        'orders', 'active_orders', 'complete_orders', 'deliver_orders', 'cancel_orders', 'all_orders', 'pending_orders'
    ));
}

    public function sellerJobOrders(Request $request)
    {

        if (!empty($request->order_id || $request->order_date || $request->payment_status || $request->order_status || $request->total || $request->job_title || $request->seller_name)) {

            $orders_query = Order::with('online_order_ticket')
                ->where('seller_id', Auth::guard('web')->user()->id)
                ->where('job_post_id', '!=', NULL);

            // search by order ID
            if (!empty($request->order_id)) {
                $orders_query->where('id', $request->order_id);
            }
            // search by order create date
            if (!empty($request->order_date)) {
                $start_date = \Str::of($request->order_date)->before('to');
                $end_date = \Str::of($request->order_date)->after('to');
                $orders_query->whereBetween('created_at', [$start_date, $end_date]);
            }
            // search by payment status
            if (!empty($request->payment_status)) {
                $orders_query->where('payment_status', $request->payment_status);
            }

            // search by order status
            if (!empty($request->order_status)) {
                if ($request->order_status == 'pending') {
                    $orders_query->where('status', 0);
                } else {
                    $orders_query->where('status', $request->order_status);
                }
            }

            // search by order amount
            if (!empty($request->total)) {
                $orders_query->where('payment_status', $request->total);
            }

            // search by job title
            if (!empty($request->job_title)) {
                $job_id = BuyerJob::select('id', 'title')->where('title',  'LIKE', "%{$request->job_title}%")->pluck('id')->toArray();
                $orders_query->whereIn('job_post_id', $job_id);
            }

            // search by seller name
            if (!empty($request->buyer_name)) {
                $buyer_id = User::select('id', 'name')->where('name',  'LIKE', "%{$request->buyer_name}%")->pluck('id')->toArray();
                $orders_query->whereIn('buyer_id', $buyer_id);
            }

            $all_orders = $orders_query->latest()->paginate(10);
        } else {
            $all_orders = Order::with('online_order_ticket')->where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->latest()->paginate(10);
        }

        $orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->get();
        $pending_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 0);
        $active_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 1);
        $complete_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 2);
        $deliver_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 3);
        $cancel_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 4);

        return view('frontend.user.seller.order.orders', compact('orders', 'active_orders', 'complete_orders', 'deliver_orders', 'cancel_orders', 'all_orders', 'pending_orders'));
    }

    public function checkOtpRequired(Request $request)
    {
        try {
            $order = Order::findOrFail($request->order_id);

            if (!$order->job_post_id) {
                return response()->json(['otp_required' => false, 'message' => 'No job post associated with this order.']);
            }

            // Check global OTP verification setting
            $otp_required = get_static_option('job_otp_verification_required') === 'active';

            return response()->json([
                'otp_required' => $otp_required,
                'message' => 'OTP check completed.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function sendJobComplteConfirmOtp(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'order_id' => 'required|exists:orders,id',
            ]);

            $order = Order::findOrFail($request->order_id);

            // Find the associated job request
            $job = JobRequest::where('job_post_id', $order->job_post_id)
                ->where('seller_id', $order->seller_id)
                ->first();

            if (!$job) {
                return response()->json(['message' => 'Job request not found.'], 404);
            }

            // Generate OTP (6-digit number)
            $otp = rand(100000, 999999);

            // Update job with OTP and expiration time (60 minutes from now)
            $updated = $job->update([
                'completion_otp' => $otp,
                'otp_expires_at' => now()->addMinutes(60),
            ]);

            if (!$updated) {
                return response()->json(['message' => 'Failed to save OTP.'], 500);
            }

            // Get buyer
            $buyer = User::find($order->buyer_id);

            if (!$buyer || empty($buyer->phone)) {
                return response()->json(['message' => 'Unable to send OTP: Buyer phone number not available.'], 400);
            }

            // Send OTP SMS to buyer only
            try {
                $message = __('Your OTP for order completion is: ') . $otp . __(' for Order ID: ') . $order->id;
                $smsService = new SMSService();
                $smsService->send_sms($buyer->phone, $message);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Failed to send OTP SMS: ' . $e->getMessage()], 500);
            }

            return response()->json(['message' => 'OTP sent successfully.']);
        } catch (\Exception $e) {
            \Log::error('Error in sendJobComplteConfirmOtp: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function activeOrders()
    {
        $orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL);
        $active_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL)->where('status', 1)->paginate(10);
        $complete_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL)->where('status', 2);
        $deliver_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL)->where('status', 3);
        $cancel_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL)->where('status', 4);
        return view('frontend.user.seller.order.active-orders', compact('orders', 'active_orders', 'complete_orders', 'deliver_orders', 'cancel_orders'));
    }

    public function activeJobOrders()
    {
        $orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL);
        $active_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 1)->paginate(10);
        $complete_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 2);
        $deliver_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 3);
        $cancel_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 4);
        return view('frontend.user.seller.order.active-orders', compact('orders', 'active_orders', 'complete_orders', 'deliver_orders', 'cancel_orders'));
    }

    public function completeOrders()
    {
        $orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL);
        $active_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL)->where('status', 1);
        $complete_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL)->where('status', 2)->paginate(10);
        $deliver_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL)->where('status', 3);
        $cancel_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL)->where('status', 4);
        return view('frontend.user.seller.order.complete-orders', compact('orders', 'active_orders', 'complete_orders', 'deliver_orders', 'cancel_orders'));
    }

    public function completeJobOrders()
    {
        $orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL);
        $active_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 1);
        $complete_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 2)->paginate(10);
        $deliver_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 3);
        $cancel_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 4);
        return view('frontend.user.seller.order.complete-orders', compact('orders', 'active_orders', 'complete_orders', 'deliver_orders', 'cancel_orders'));
    }

    public function deliverOrders()
    {
        $orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL);
        $active_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL)->where('status', 1);
        $complete_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL)->where('status', 2);
        $deliver_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL)->where('status', 3)->paginate(10);
        $cancel_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL)->where('status', 4);
        return view('frontend.user.seller.order.deliver-orders', compact('orders', 'active_orders', 'complete_orders', 'deliver_orders', 'cancel_orders'));
    }

    public function deliverJobOrders()
    {
        $orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL);
        $active_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 1);
        $complete_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 2);
        $deliver_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 3)->paginate(10);
        $cancel_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 4);
        return view('frontend.user.seller.order.deliver-orders', compact('orders', 'active_orders', 'complete_orders', 'deliver_orders', 'cancel_orders'));
    }

    public function cancelOrders()
    {
        $orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL);
        $active_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL)->where('status', 1);
        $complete_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL)->where('status', 2);
        $deliver_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL)->where('status', 3);
        $cancel_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', NULL)->where('status', 4)->paginate(10);
        return view('frontend.user.seller.order.cancel-orders', compact('orders', 'active_orders', 'complete_orders', 'deliver_orders', 'cancel_orders'));
    }

    public function cancelJobOrders()
    {
        $orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL);
        $active_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 1);
        $complete_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 2);
        $deliver_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 3);
        $cancel_orders = Order::where('seller_id', Auth::guard('web')->user()->id)->where('job_post_id', '!=', NULL)->where('status', 4)->paginate(10);
        return view('frontend.user.seller.order.cancel-orders', compact('orders', 'active_orders', 'complete_orders', 'deliver_orders', 'cancel_orders'));
    }

    public function orderDetails($id = null)
    {
        $order_details = Order::where('id', $id)->where('seller_id', Auth::guard('web')->user()->id)->first();
        $order_declines_history = OrderCompleteDecline::where('order_id', $id)->latest()->get();
        $showresult = StaticOption::where('option_name', "result")->select("option_value")->first();
        $show = false;
        $showResult = $showresult ? $showresult->option_value : 'nothing';
        if ($showResult == "Checkbox is checked.") {
            $show = true;
        }

        if (!empty($order_details)) {
            $order_includes = OrderInclude::where('order_id', $id)->get();
            $order_additionals = OrderAdditional::where('order_id', $id)->get();
            foreach (Auth::guard('web')->user()->unreadNotifications()->where('data->order_message', 'You have a new order')->get() as $notification) {
                if ($order_details->id == $notification->data['order_id']) {
                    $Notification = Auth::guard('web')->user()->Notifications->find($notification->id);
                    if ($Notification) {
                        $Notification->markAsRead();
                    }
                    return view('frontend.user.seller.order.order-details', compact('show', 'order_details', 'order_includes', 'order_additionals', 'order_declines_history'));
                }
            }
            return view('frontend.user.seller.order.order-details', compact('show', 'order_details', 'order_includes', 'order_additionals', 'order_declines_history'));
        } else {
            abort(404);
        }
    }

    public function orderStatus(Request $request, $id = null)
    {
        if (empty($request->status)) {
            toastr_error(__('Please select status first.'));
            return redirect()->back();
        }

        $order = Order::where('id', $request->order_id)->first();

        if (!$order) {
            toastr_error(__('Order not found.'));
            return redirect()->back();
        }

        if ($order->cancel_order_money_return === 1) {
            toastr_error(__('You cannot change status because the order was already cancelled.'));
            return redirect()->back();
        }

        // Prevent status change if already completed
        if ($order->status == 2) {
            toastr_error(__('You cannot change status because this order is already completed.'));
            return redirect()->back();
        }

        // Handle Rejected Status
        if ($request->status == 3) {
            // update reject request
            Order::where('id', $order->id)->update(['order_complete_request' => 4, 'status' => 4]);

            $buyer = User::find($order->buyer_id);

            $shouldRefund = $order->payment_status === 'complete' && $order->payment_gateway !== 'cash_on_delivery';

            if ($shouldRefund && $buyer) {
                $wallet = Wallet::firstOrCreate(['buyer_id' => $buyer->id], ['balance' => 0]);
                $wallet->increment('balance', $order->total);

                WalletHistory::create([
                    'buyer_id' => $buyer->id,
                    'payment_gateway' => $order->payment_gateway,
                    'payment_status' => 'complete',
                    'amount' => $order->total,
                    'transaction_id' => 'REFUND-ORDER-' . $order->id,
                    'manual_payment_image' => '0',
                    'status' => 1,
                ]);
            }

            toastr_success(__('Order has been rejected and refunded to buyer wallet.'));
            return redirect()->back();
        }

        // Only allow order complete request if payment is complete
        if ($request->status == 2) {
            if ($order->payment_status !== 'complete') {
                toastr_error(__('You cannot change order status due to payment status pending.'));
                return redirect()->back();
            }
            // Check if OTP is required
            $otp_required = get_static_option('job_otp_verification_required') === 'active';

            if ($otp_required && $order->job_post_id) {
                // Find the associated job request
                $job = JobRequest::where('job_post_id', $order->job_post_id)
                    ->where('seller_id', $order->seller_id)
                    ->first();

                if (!$job) {
                    toastr_error(__('Job request not found.'));
                    return redirect()->back();
                }

                // Verify OTP
                if (empty($request->otp)) {
                    toastr_error(__('Please enter the OTP.'));
                    return redirect()->back();
                }

                if (!$job->completion_otp || $job->completion_otp != $request->otp) {
                    toastr_error(__('Invalid OTP.'));
                    return redirect()->back();
                }

                if (now()->gt($job->otp_expires_at)) {
                    toastr_error(__('OTP has expired. Please request a new one.'));
                    return redirect()->back();
                }

                // OTP is valid, clear it after successful verification
                $job->update([
                    'completion_otp' => null,
                    'otp_expires_at' => null,
                ]);
                Order::where('id', $order->id)->update(['order_complete_request' => 2, 'status'=>2]);
            } else {
                Order::where('id', $order->id)->update(['order_complete_request' => 1]);
                $buyer = $order->buyer;
                if ($buyer) {
                    $pushTitle = __('Order Completion Requested');
                    $pushBody  = __('Seller has requested completion for Order #:id', [
                        'id' => $order->id
                    ]);

                    $extraData = [
                        'order_id' => $order->id,
                        'seller_id'=> $order->seller_id,
                        'buyer_id' => $buyer->id,
                        'type'     => 'OrderCompletion',
                    ];

                    send_push_notification(
                        $buyer,        // notifiable
                        $order->id,    // identity
                        'OrderCompletionRequest', // type (or create 'OrderCompletion' if you prefer)
                        $pushBody,     // body
                        $pushTitle,    // title
                        $extraData,
                        $order,
                        true
                    );
                }
            }

            OrderCompleteDecline::create([
                'order_id' => $order->id,
                'buyer_id' => $order->buyer_id,
                'seller_id' => $order->seller_id,
                'service_id' => $order->service_id,
                'decline_reason' => 'Not declined yet',
                'image' => $request->image,
            ]);

            $buyer = User::find($order->buyer_id);

            if ($buyer) {
                $message = __('You have a new order complete request');
                $buyer->notify(new OrderNotification($order->id, $order->service_id, $order->seller_id, $order->buyer_id, $message));
            }

            if (!$otp_required) {
                try {
                    $message_body_buyer = __('Hello,') . $order->name . __(' A new request is created to complete an order.') . '<br>' . '<span class="verify-code">' . __('Order ID: ') . $order->id . '</span>';
                    $message_body_admin = __('Hello Admin, a new request is created to complete an order.') . '<br>' . '<span class="verify-code">' . __('Order ID: ') . $order->id . '</span>';

                    Mail::to($order->email)->queue(new BasicMail([
                        'subject' => __('New Request For Complete an Order'),
                        'message' => $message_body_buyer
                    ]));

                    Mail::to(get_static_option('site_global_email'))->queue(new BasicMail([
                        'subject' => __('New Request For Complete an Order'),
                        'message' => $message_body_admin
                    ]));

                    $smsService = new SMSService();
                    $smsService->send_sms($buyer->phone, strip_tags($message_body_buyer));

                    $admins = Admin::where('role', 'Super Admin')->get();
                    foreach ($admins as $admin) {
                        $smsService->send_sms($admin->phone, strip_tags($message_body_admin));
                    }
                    toastr_success(__('Your request has been submitted. Buyer will review and complete the order.'));
                } catch (\Exception $e) {
                    return redirect()->back()->with(FlashMsg::item_new($e->getMessage()));
                }
            } else {

                $message_body_buyer = __('Hello,') . $order->name . __(' A new request is created to complete an order.') . '<br>' . '<span class="verify-code">' . __('Order ID: ') . $order->id . '</span>';
                $message_body_admin = __('Hello Admin, a new request is created to complete an order.') . '<br>' . '<span class="verify-code">' . __('Order ID: ') . $order->id . '</span>';

                Mail::to($order->email)->queue(new BasicMail([
                    'subject' => __('New Request For Complete an Order'),
                    'message' => $message_body_buyer
                ]));

                Mail::to(get_static_option('site_global_email'))->queue(new BasicMail([
                    'subject' => __('New Request For Complete an Order'),
                    'message' => $message_body_admin
                ]));
              toastr_success(__('Your request has been submitted.'));   
            }
            return redirect()->back();
        }

        toastr_error(__('Invalid status change request.'));
        return redirect()->back();
    }


    public function orderCancel($id = null)
    {
        $order_details = Order::select(['id', 'seller_id', 'buyer_id', 'service_id',
            'payment_status', 'payment_gateway', 'total'])->where('id', $id)->first();
        $buyer = User::where('id', $order_details->buyer_id)->first();
        $shouldRefund = $order_details->payment_status == 'complete' && $order_details->payment_gateway != 'cash_on_delivery';
        Order::where('id', $id)->update(['payment_status' => '', 'status' => 4]);
        if ($shouldRefund) {
            $wallet = Wallet::firstOrCreate(
                ['buyer_id' => $buyer->id],
                ['balance' => 0]
            );

            $wallet->increment('balance', $order_details->total);

            // 2. Create wallet history
            WalletHistory::create([
                'buyer_id' => $buyer->id,
                'payment_gateway' => $order_details->payment_gateway,
                'payment_status' => 'complete',
                'amount' => $order_details->total,
                'transaction_id' => 'REFUND-ORDER-' . $order_details->id,
                'manual_payment_image' => '0',
                'status' => 1,
            ]);
            Order::where('id', $id)->update(['cancel_order_money_return' => 1]);
        }
        $message_body_buyer = __('Hello,') . __('A order is canceled.') . __('Order ID is:') . $id;
        $message_body_admin = __('Hello Admin An order is canceled.') . __('Order ID is:') . $id;
        $smsService = new SMSService();
        //send sms to buyer
        $buyer_phone = $buyer->phone;
        $smsService->send_sms($buyer_phone,  $message_body_buyer);
        /*
        $number = '';

        $smsService->send_sms($number,  $message_body_buyer);*/


        $admins = Admin::all();
        $message_for_super_admin = $message_body_admin;
        foreach ($admins as $admin)  // Send SMS to all super admin
        {
            if ($admin->role == "Super Admin") {

                $smsService->send_sms($admin->phone,  $message_for_super_admin);
                //smsService->send_sms($number,  $message_for_super_admin);
            }
        }
        // 🔔 Send push notification to buyer
        $buyer = $order_details->buyer;
        if ($buyer) {
            $pushTitle = __('Your order #:id is cancelled', [
                'id' => $order_details->id
            ]);
            $pushBody  = __('Your Service Order #:id is Cancelled By the Seller.', [
                'id' => $order_details->id
            ]);

            $extraData = [
                'order_id'  => $order_details->id,
                'seller_id' => $order_details->seller_id,
                'buyer_id'  => $order_details->buyer_id,
                'type'      => 'OrderStatus',
            ];

            send_push_notification(
                $buyer,                  // Notifiable (seller)
                $order_details->id,               // Identity
                'OrderStatus',// Type
                $pushBody,                // Body
                $pushTitle,               // Title
                $extraData,
                $order_details,
                true
            );
        }

        toastr_success(__('Order successfully cancelled.'));
        return redirect()->back();
    }
    
    public function orderAccept($id = null)
    {
        $order_details = Order::select(['id', 'seller_id', 'buyer_id', 'service_id'])->where('id', $id)->first();
        $buyer = User::where('id', $order_details->buyer_id)->first();
        Order::where('id', $id)->update(['payment_status' => 'pending', 'status' => 5]);
        $message_body_buyer = __('Hello,') . __('A order is accepted.') . __('Order ID is:') . $id;
        $message_body_admin = __('Hello Admin An order is accepted.') . __('Order ID is:') . $id;
        $smsService = new SMSService();
        //send sms to buyer
        $buyer_phone = $buyer->phone;
        $smsService->send_sms($buyer_phone,  $message_body_buyer);
        /*
        $number = '';

        $smsService->send_sms($number,  $message_body_buyer);*/


        $admins = Admin::all();
        $message_for_super_admin = $message_body_admin;
        foreach ($admins as $admin)  // Send SMS to all super admin
        {
            if ($admin->role == "Super Admin") {

                $smsService->send_sms($admin->phone,  $message_for_super_admin);
                //smsService->send_sms($number,  $message_for_super_admin);
            }
        }


        // 🔔 Send push notification to seller
        $buyer = $order_details->buyer;
        if ($buyer) {
            $pushTitle = __('Your order is accepted');
            $pushBody  = __('Your Service Order is Accepted By the Seller.', [
                'id' => $order_details->id
            ]);

            $extraData = [
                'order_id'  => $order_details->id,
                'seller_id' => $buyer->id,
                'buyer_id'  => $order_details->buyer_id,
                'type'      => 'OrderStatus',
            ];

            send_push_notification(
                $buyer,                  // Notifiable (seller)
                $order_details->id,               // Identity
                '',// Type
                $pushBody,                // Body
                $pushTitle,               // Title
                $extraData,
                $order_details,
                true
            );
        }

        toastr_success(__('Order successfully Accepted.'));
        return redirect()->back();
    }

    public function orderPaymentStatus(Request $request, $id = null)
    {

        $this->validate($request, [
            'order_id' => 'required',
            'status' => 'required|string'
        ]);
        $payment_status = Order::select('payment_status', 'status', 'job_post_id')->where(['id' => $request->order_id, 'seller_id' => Auth::guard('web')->id()])->first();

        if (!is_null($payment_status)) {
            Order::where(['id' => $request->order_id, 'seller_id' => Auth::guard('web')->id()])->update([
                'payment_status' =>  $request->status
            ]);
        }
        $order_details = Order::select(['id', 'buyer_id'])->where('id', $request->order_id)->first();
        $buyer = User::where('id', $order_details->buyer_id)->first();
        $message_body_buyer = __('Hello,') . __('A payment status is changed.') . __('Order ID is:') . $request->order_id;
        $message_body_admin = __('Hello Admin A payment status is changed.') . __('Order ID is:') . $request->order_id;
        $smsService = new SMSService();
        //send sms to buyer
        $buyer_phone = $buyer->phone;
        $smsService->send_sms($buyer_phone,  $message_body_buyer);

        //smsService->send_sms($number,  $message_body_buyer);


        $admins = Admin::all();
        $message_for_super_admin = $message_body_admin;
        foreach ($admins as $admin)  // Send SMS to all super admin
        {

            if ($admin->role == "Super Admin") {

                $smsService->send_sms($admin->phone,  $message_for_super_admin);
                //smsService->send_sms($number,  $message_for_super_admin);
            }
        }

        // 🔔 Send push notification to buyer
        $buyer = $order_details->buyer;
        if ($buyer) {
            $pushTitle = __('Payment Status Changed');
            $pushBody  = __('Manual Payment Status Changed To Completed', [
                'id' => $order_details->id
            ]);

            $extraData = [
                'order_id'  => $order_details->id,
                'seller_id' => $order_details->seller_id,
                'buyer_id'  => $order_details->buyer_id,
                'type'      => 'PaymentStatusChanged',
            ];

            send_push_notification(
                $buyer,                  // Notifiable (buyer)
                $order_details->id,               // Identity
                '',// Type
                $pushBody,                // Body
                $pushTitle,               // Title
                $extraData,
                $order_details,
                true
            );
        }
        toastr_success(sprintf(__('Payment Status Has been changed to %s'), $request->status));
        return redirect()->back();
    }

    //seller report
    public function reportUs(Request $request)
    {
        $request->validate([
            'report' => 'required',
        ]);

        $seller_id = Auth::guard()->check() ? Auth::guard('web')->user()->id : NULL;
        $is_report_exist = Report::where(['order_id' => $request->order_id, 'report_from' => 'seller'])->first();

        if ($is_report_exist) {
            toastr_error(__('Report Already Created For This Order'));
            return redirect()->back();
        }
        $report = Report::create([
            'order_id' => $request->order_id,
            'service_id' => $request->service_id,
            'seller_id' => $seller_id,
            'buyer_id' => $request->buyer_id,
            'report_from' => 'seller',
            'report_to' => 'buyer',
            'report' => $request->report,
        ]);

        $last_report_id = $report->id;
        try {
            $message = get_static_option('seller_report_message');
            $message = str_replace(["@report_id"], [$last_report_id], $message);
            Mail::to(get_static_option('site_global_email'))->queue(new BasicMail([
                'subject' => get_static_option('seller_report_subject') ?? __('Seller New Report'),
                'message' => $message
            ]));
        } catch (\Exception $e) {
            return redirect()->back()->with(FlashMsg::item_new($e->getMessage()));
        }
        toastr_success(__('Report Send Success'));
        return redirect()->back();
    }

    public function reportList(Request $request)
    {
        if (!empty($request->order_id || $request->report_id || $request->report_date)) {
            $reports_query = Report::where('seller_id', Auth::guard('web')->user()->id);
            if (!empty($request->order_id)) {
                $reports_query->where('order_id', $request->order_id);
            }
            if (!empty($request->report_id)) {
                $reports_query->where('id', $request->report_id);
            }
            // search by date range
            if (!empty($request->report_date)) {
                $start_date = \Str::of($request->report_date)->before('to');
                $end_date = \Str::of($request->report_date)->after('to');
                $reports_query->whereBetween('created_at', [$start_date, $end_date]);
            }
            $reports = $reports_query->paginate(10);
        } else {
            $reports = Report::where('seller_id', Auth::guard('web')->user()->id)->paginate(10);
        }

        return view('frontend.user.seller.report.report-list', compact('reports'));
    }

    public function chat_to_admin(Request $request, $report_id)
    {
        $seller_id = Auth::guard('web')->user()->id;
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'message' => 'required',
                'notify' => 'nullable|string',
                'attachment' => 'nullable|mimes:zip,jpg,jpeg,png,pdf,webp,xlsx, csv, xls,docx',
            ]);

            $ticket_info = ReportChatMessage::create([
                'report_id' => $report_id,
                'seller_id' => $seller_id,
                'message' => $request->message,
                'type' => 'seller',
                'notify' => $request->send_notify_mail ? 'on' : 'off',
            ]);

            if ($request->hasFile('attachment')) {
                $uploaded_file = $request->attachment;
                $file_extension = $uploaded_file->extension();
                $file_name =  pathinfo($uploaded_file->getClientOriginalName(), PATHINFO_FILENAME) . time() . '.' . $file_extension;

                // file scan start
                $file_extension = $uploaded_file->getClientOriginalExtension();
                if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $processed_image = Image::make($uploaded_file);
                    $image_default_width = $processed_image->width();
                    $image_default_height = $processed_image->height();

                    $processed_image->resize($image_default_width, $image_default_height, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $processed_image->save('assets/uploads/ticket/' . $file_name);
                } else {
                    $uploaded_file->move('assets/uploads/ticket', $file_name);
                } // file scan end

                $ticket_info->attachment = $file_name;
                $ticket_info->save();
            }

            //send mail to user
            //            event(new SupportMessage($ticket_info));
            return redirect()->back()->with(FlashMsg::item_new(__('Message Send')));
        }
        $report_details = Report::where('id', $report_id)->where('seller_id', $seller_id)->first();
        $all_messages = ReportChatMessage::where('report_id', $report_id)
            ->where('seller_id', $seller_id)
            ->get();
        $q = $request->q ?? '';
        return view('frontend.user.seller.report.report-chat', compact('report_details', 'all_messages', 'q'));
    }

    //payout request 
    public function payoutRequest(Request $request, $id = null)
    {

        $seller_id = Auth::guard('web')->user()->id;

        if (!empty($request->payout_history_id || $request->status || $request->payout_request_date)) {
            $payout_history_query = PayoutRequest::where('seller_id', $seller_id);

            if (!empty($request->payout_history_id)) {
                $payout_history_query->where('id', $request->payout_history_id);
            }
            if (!empty($request->status)) {
                if ($request->status == 'pending') {
                    $payout_history_query->where('status', 0);
                } else {
                    $payout_history_query->where('status', $request->status);
                }
            }
            // search by date range
            if (!empty($request->payout_request_date)) {
                $start_date = \Str::of($request->payout_request_date)->before('to');
                $end_date = \Str::of($request->payout_request_date)->after('to');
                $payout_history_query->whereBetween('created_at', [$start_date, $end_date]);
            }
            $all_payout_request = $payout_history_query->paginate(10);
        } else {
            $all_payout_request = PayoutRequest::where('seller_id', $seller_id)->paginate(10);
        }

        $total_earnings = 0;
        $pending_order = Order::where(['status' => 0, 'seller_id' => $seller_id])->count();
        $complete_order = Order::where(['status' => 2, 'seller_id' => $seller_id])->count();

        $get_sum = Order::with('extraSevices')->where(['status' => 2, 'seller_id' => $seller_id])->get();
        $total_order_amount = $get_sum->sum('total');

        $extra_service_total = 0;
        $extra_service_total_tax = 0;
        $extra_service_total_commission_amount = 0;
        // seller total order amount & total extra service amount sum
        foreach ($get_sum as $order) {
            if ($order->extraSevices) {
                foreach ($order->extraSevices as $extraService) {
                    if ($extraService->payment_status == 'complete') {
                        $extra_service_total += $extraService->total;
                        $extra_service_total_tax += $extraService->tax;
                        $extra_service_total_commission_amount += $extraService->commission_amount;
                    }
                }
            }
        }

        $complete_order_balance_with_tax = $total_order_amount + $extra_service_total;

        $complete_order_tax = Order::where(['status' => 2, 'seller_id' => $seller_id])->sum('tax') + $extra_service_total_tax;
        $complete_order_balance_without_tax = $complete_order_balance_with_tax - $complete_order_tax;
        $admin_commission_amount = Order::where(['status' => 2, 'seller_id' => $seller_id])->sum('commission_amount') + $extra_service_total_commission_amount;

        $remaning_balance = $complete_order_balance_without_tax - $admin_commission_amount;
        $total_earnings = PayoutRequest::where('seller_id', $seller_id)->sum('amount');

        return view('frontend.user.seller.payout.payout-request', compact(
            'pending_order',
            'complete_order',
            'remaning_balance',
            'all_payout_request',
            'total_earnings'
        ));
    }

    public function createPayoutRequest(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'amount' => 'required|numeric',
                'payment_gateway' => 'required|string|max:191',
            ], [
                'amount.required' => __('Amount required'),
                'amount.numeric' => __('Amount must be numeric'),
                'payment_gateway.required' =>  __('Payment Gateway required'),
            ]);

            $seller_id = Auth::guard('web')->user()->id;

            $complete_order_balance_with_tax = Order::where(['status' => 2, 'seller_id' => $seller_id])->sum('total');
            $complete_order_tax = Order::where(['status' => 2, 'seller_id' => $seller_id])->sum('tax');
            $complete_order_balance_without_tax = $complete_order_balance_with_tax - $complete_order_tax;
            $admin_commission_amount = Order::where(['status' => 2, 'seller_id' => $seller_id])->sum('commission_amount');
            $remaning_balance = $complete_order_balance_without_tax - $admin_commission_amount;
            $total_earnings = PayoutRequest::where('seller_id', $seller_id)->sum('amount');

            $available_balance = $remaning_balance - $total_earnings;
            if ($request->amount <= 0 || $request->amount > $available_balance) {
                toastr_error(__('Enter a valid amount'));
                return redirect()->back();
            }

            $min_amount = AmountSettings::select('min_amount')->first();
            $max_amount = AmountSettings::select('max_amount')->first();
            if ($request->amount < $min_amount->min_amount) {
                $msg = sprintf(__('Withdraw amount not less than %s'), float_amount_with_currency_symbol($min_amount->min_amount));
                toastr_error($msg);
                return redirect()->back();
            }
            if ($request->amount > $max_amount->max_amount) {
                $msg = sprintf(__('Withdraw amount must less or equal to %s'), float_amount_with_currency_symbol($max_amount->max_amount));
                toastr_error($msg);
                return redirect()->back();
            }

            PayoutRequest::create([
                'seller_id' => Auth::guard('web')->user()->id,
                'amount' => $request->amount,
                'payment_gateway' => $request->payment_gateway,
                'seller_note' => $request->seller_note,
                'status' => 0,
            ]);

            $last_payout_request_id = DB::getPdo()->lastInsertId();
            try {
                $message = get_static_option('seller_payout_message');
                $message = str_replace(["@payout_request_id"], [$last_payout_request_id], $message);
                Mail::to(get_static_option('site_global_email'))->queue(new BasicMail([
                    'subject' => get_static_option('seller_payout_subject') ?? __('New Payout Request'),
                    'message' => $message
                ]));
            } catch (\Exception $e) {
                return redirect()->back()->with(FlashMsg::item_new($e->getMessage()));
            }

            toastr_success(__('Payment request create success'));
            return redirect()->back();
        }
    }

    public function PayoutRequestDetails($id = null)
    {
        $request_details = PayoutRequest::where('id', $id)
            ->where('seller_id', Auth::guard('web')
                ->user()->id)
            ->first();
        if ($request_details != '') {
            return view('frontend.user.seller.payout.payout-request-details', compact('request_details'));
        } else {
            abort(404);
        }
    }

    //reviews 
    public function serviceReview(Request $request)
    {

        if (!empty($request->title || $request->service_date)) {
            $service_review_query = Service::whereHas('reviews')->where('seller_id', Auth::user()->id);
            if (!empty($request->title)) {
                $service_review_query->where('title', 'LIKE', "%{$request->title}%");
            }
            // search by date range
            if (!empty($request->service_date)) {
                $start_date = \Str::of($request->service_date)->before('to');
                $end_date = \Str::of($request->service_date)->after('to');
                $service_review_query->whereBetween('created_at', [$start_date, $end_date]);
            }
            $services = $service_review_query->paginate(10);
        } else {
            $services = Service::whereHas('reviews')->where('seller_id', Auth::user()->id)->paginate(10);
        }

        return view('frontend.user.seller.services.service-reviews', compact('services'));
    }

    public function serviceReviewAll($id = null)
    {

        $service_reviews = Review::where('service_id', $id)
            ->where('seller_id', Auth::guard('web')->user()->id)->where('type', 1)
            ->paginate(10);

        return view('frontend.user.seller.services.service-all-reviews', compact('service_reviews'));
    }

    public function reviewDelete($id = null)
    {
        Review::find($id)->delete();
        toastr_error(__('Review Delete Success---'));
        return redirect()->back();
    }

    public function allTickets(Request $request)
    {
        if (!empty($request->title || $request->order_id || $request->ticket_id || $request->ticket_date)) {
            $tickets_query = SupportTicket::where('seller_id', Auth::guard('web')->user()->id);
            if (!empty($request->title)) {
                $tickets_query->where('title', 'LIKE', "%{$request->title}%");
            }
            if (!empty($request->order_id)) {
                $tickets_query->where('order_id', $request->order_id);
            }
            if (!empty($request->ticket_id)) {
                $tickets_query->where('id', $request->ticket_id);
            }

            // search by date range
            if (!empty($request->ticket_date)) {
                $start_date = \Str::of($request->ticket_date)->before('to');
                $end_date = \Str::of($request->ticket_date)->after('to');
                $tickets_query->whereBetween('created_at', [$start_date, $end_date]);
            }

            $tickets = $tickets_query->orderBy('id', 'desc')->paginate(10);
        } else {
            $tickets = SupportTicket::where('seller_id', Auth::guard('web')->user()->id)->orderBy('id', 'desc')->paginate(10);
        }

        $orders = Order::where('seller_id', Auth::guard('web')->user()->id)
            ->where('payment_status', '!=', '')
            ->whereNotNull('buyer_id',)
            ->latest()->get();
        return view('frontend.user.seller.support-ticket.all-tickets', compact('tickets', 'orders'));
    }

    public function addNewTicket(Request $request, $id = null)
    {
        if ($request->isMethod('post')) {

            $this->validate($request, [
                'title' => 'required|string|max:191',
                'subject' => 'required|string|max:191',
                'priority' => 'required|string|max:191',
                'order_id' => 'required'
            ], [
                'title.required' => __('title required'),
                'subject.required' =>  __('subject required'),
                'priority.required' =>  __('priority required'),
            ]);

            $seller_id = Auth::guard('web')->user()->id;
            if ($request->order_id) {
                $buyer_id = Order::select('buyer_id')->where('id', $request->order_id)->first();
            }

            SupportTicket::create([
                'title' => $request->title,
                'description' => $request->description,
                'subject' => $request->subject,
                'status' => 'open',
                'priority' => $request->priority,
                'seller_id' => $seller_id,
                'buyer_id' => $buyer_id->buyer_id,
                'order_id' => $request->order_id,
            ]);
            toastr_success(__('Ticket successfully created.'));
            $last_ticket_id = DB::getPdo()->lastInsertId();
            $last_ticket = SupportTicket::where('id', $last_ticket_id)->first();

            // send order ticket notification to buyer
            $buyer = User::where('id', $last_ticket->buyer_id)->first();
            if ($buyer) {
                $order_ticcket_message = __('You have a new order ticket');
                $buyer->notify(new TicketNotification($last_ticket_id, $seller_id, $last_ticket->buyer_id, $order_ticcket_message));

                // Push notification
                $pushTitle = __('New Support Ticket');
                $pushBody  = __('Seller has created a support ticket for your order #:id', [
                    'id' => $last_ticket->order_id
                ]);

                $extraData = [
                    'ticket_id' => $last_ticket_id,
                    'order_id'  => $last_ticket->order_id,
                    'seller_id' => $seller_id,
                    'buyer_id'  => $buyer->id,
                    'type'      => 'SupportTicket',
                ];

                send_push_notification(
                    $buyer,             // notifiable
                    $last_ticket_id,    // identity (could also be $last_ticket->order_id if you want)
                    'SupportTicket',    // type
                    $pushBody,          // body
                    $pushTitle,         // title
                    $extraData,
                    $last_ticket
                );
            }
            // admin notification add
            AdminNotification::create(['ticket_id' => $last_ticket_id]);

            //Send ticket mail to buyer and admin
            try {
                $message = get_static_option('seller_order_ticket_message');
                $message = str_replace(["@order_ticket_id"], [$last_ticket_id], $message);
                Mail::to(get_static_option('site_global_email'))->queue(new BasicMail([
                    'subject' => get_static_option('order_ticket_subject') ?? __('New Order Ticket'),
                    'message' => $message
                ]));
                Mail::to($buyer->email)->queue(new BasicMail([
                    'subject' => get_static_option('seller_order_ticket_subject') ?? __('New Order Ticket'),
                    'message' => $message
                ]));
            } catch (\Exception $e) {
                return redirect()->back()->with(FlashMsg::item_new($e->getMessage()));
            }

            return redirect()->back();
        }

        $order = Order::select('id', 'service_id', 'buyer_id')
            ->where('id', $id)
            ->where('seller_id', Auth::guard('web')->user()->id)
            ->first();
        return view('frontend.user.seller.support-ticket.add-new-ticket', compact('order'));
    }

    public function ticketDelete($id = null)
    {
        SupportTicket::find($id)->delete();
        toastr_error(__('Ticket Delete Success---'));
        return redirect()->back();
    }

    //view ticket 
    public function view_ticket(Request $request, $id)
    {
        $ticket_details = SupportTicket::findOrFail($id);
        $all_messages = SupportTicketMessage::where(['support_ticket_id' => $id])->get();
        $q = $request->q ?? '';

        foreach (Auth::guard('web')->user()->notifications as $notification) {
            if ($ticket_details->id == array_key_exists("seller_last_ticket_id", $notification->data)) {
                $Notification = Auth::guard('web')->user()->Notifications->find($notification->id);
                if ($Notification) {
                    $Notification->markAsRead();
                }
                return view('frontend.user.seller.support-ticket.view-ticket', compact('ticket_details', 'all_messages', 'q'));
            }
        }
        return view('frontend.user.seller.support-ticket.view-ticket', compact('ticket_details', 'all_messages', 'q'));
    }

    //priority status 
    public function priorityChange(Request $request)
    {
        SupportTicket::where('id', $request->ticket_id)->update(['priority' => $request->priority]);
        toastr_success(__('Priority Change Success---'));
        return redirect()->back();
    }

    //change status 
    public function statusChange($id = null)
    {
        $status = SupportTicket::find($id);
        if ($status->status == 'open') {
            $status = 'close';
        } else {
            $status = 'open';
        }
        SupportTicket::where('id', $id)->update(['status' => $status]);
        toastr_success(__('Status Change Success---'));
        return redirect()->back();
    }

    //send message 
    public function support_ticket_message(Request $request)
    {
        $this->validate($request, [
            'ticket_id' => 'required',
            'user_type' => 'required|string|max:191',
            'message' => 'required',
            'send_notify_mail' => 'nullable|string',
            'file' => 'nullable|mimes:zip,jpg,jpeg,png,pdf,webp,xlsx, csv, xls,docx',
        ]);

        $ticket_info = SupportTicketMessage::create([
            'support_ticket_id' => $request->ticket_id,
            'type' => $request->user_type,
            'message' => $request->message,
            'notify' => $request->send_notify_mail ? 'on' : 'off',
        ]);

        if ($request->hasFile('file')) {
            $uploaded_file = $request->file;
            $file_extension = $uploaded_file->getClientOriginalExtension();
            $file_name =  pathinfo($uploaded_file->getClientOriginalName(), PATHINFO_FILENAME) . time() . '.' . $file_extension;

            // file scan start
            $file_extension = $uploaded_file->getClientOriginalExtension();
            if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $processed_image = Image::make($uploaded_file);
                $image_default_width = $processed_image->width();
                $image_default_height = $processed_image->height();

                $processed_image->resize($image_default_width, $image_default_height, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $processed_image->save('assets/uploads/ticket/' . $file_name);
            } else {
                $uploaded_file->move('assets/uploads/ticket', $file_name);
            } // file scan end

            $ticket_info->attachment = $file_name;
            $ticket_info->save();
        }

        //send mail to user
        event(new SupportMessage($ticket_info));

        // 🔔 send push notification to buyer
        $ticket = SupportTicket::find($request->ticket_id);
        if ($ticket && $ticket->buyer_id) {
            $buyer = User::find($ticket->buyer_id);

            if ($buyer) {
                $pushTitle = __('New Support Ticket Message');
                $pushBody  = __('Seller sent you a new message in support ticket #:id', [
                    'id' => $ticket->id
                ]);

                $extraData = [
                    'ticket_id' => $ticket->id,
                    'order_id'  => $ticket->order_id,
                    'type'      => 'SupportTicketMessage',
                ];

                send_push_notification(
                    $buyer,               // notifiable
                    $ticket->id,          // identity
                    'SupportTicketMessage', // type
                    $pushBody,            // body
                    $pushTitle,           // title
                    $extraData,
                    $ticket,
                    true
                );
            }
        }
        return redirect()->back()->with(FlashMsg::item_new('Message Send'));
    }

    //to do list 
    public function toDoList(Request $request)
    {
        if (!empty($request->title || $request->status || $request->todolist_date)) {
            $todolist_query = ToDoList::where('user_id', Auth::guard('web')->user()->id);

            if (!empty($request->title)) {
                $todolist_query->where('title', 'LIKE', "%{$request->title}%");
            }
            if (!empty($request->status)) {
                if ($request->status == 'in_completed') {
                    $todolist_query->where('status', 0);
                } else {
                    $todolist_query->where('status', $request->status);
                }
            }
            // search by date range
            if (!empty($request->todolist_date)) {
                $start_date = \Str::of($request->todolist_date)->before('to');
                $end_date = \Str::of($request->todolist_date)->after('to');
                $todolist_query->whereBetween('created_at', [$start_date, $end_date]);
            }
            $to_do_list = $todolist_query->paginate(10);
        } else {
            $to_do_list = ToDoList::where('user_id', Auth::guard('web')->user()->id)->paginate(10);
        }

        return view('frontend.user.seller.to-do-list.todolist', compact('to_do_list'));
    }

    public function addTodolist(Request $request)
    {
        $request->validate([
            'description' => 'required',
        ]);

        ToDoList::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => Auth::guard('web')->user()->id,

        ]);

        toastr_success(__('To Do List Added Success---'));
        return redirect()->back();
    }

    public function updateTodolist(Request $request)
    {
        $request->validate([
            'up_description' => 'required',
        ]);

        ToDoList::where('id', $request->up_id)->update([
            'title' => $request->up_title,
            'description' => $request->up_description,
        ]);

        toastr_success(__('To Do List Update Success---'));
        return redirect()->back();
    }

    public function deleteTodolist($id = null)
    {
        ToDoList::find($id)->delete();
        toastr_error(__('To Do List Delete Success---'));
        return redirect()->back();
    }

    public function changeTodoStatus($id = null)
    {
        $status = ToDoList::select('status')->where('id', $id)->first();
        if ($status->status == 1) {
            $status = 0;
        } else {
            $status = 1;
        }
        ToDoList::where('id', $id)->update([
            'status' => $status,
        ]);
        toastr_success(__('ToDo List status Update Success---'));
        return redirect()->back();
    }

    //notifications 
    public function allNotification()
    {
        return view('frontend.user.seller.notification.all-notification');
    }

    //seller verify
    public function sellerVerify(Request $request)
    {
        $user = Auth::guard('web')->user()->id;

        if ($request->isMethod('post')) {
            $request->validate([
                'national_id' => 'required|max:191',
            ]);

            $old_image = SellerVerify::select('national_id', 'address')->where('seller_id', $user)->first();

            if (is_null($old_image)) {
                SellerVerify::create([
                    'seller_id' => $user,
                    'national_id' => $request->national_id ?? optional($old_image)->national_id,
                    'address' => $request->address ?? optional($old_image)->address,
                ]);
            } else {
                SellerVerify::where('seller_id', $user)
                    ->update([
                        'seller_id' => $user,
                        'national_id' => $request->national_id ?? optional($old_image)->national_id,
                        'address' => $request->address ?? optional($old_image)->address,
                    ]);
            }

            try {
                // Load seller details to include in the notification email
                $seller_user = Auth::guard('web')->user();
                $admin_verify_url = route('admin.frontend.seller.verify.all');

                // Use custom template if configured, otherwise build a clear default message
                $custom_message = get_static_option('seller_verification_message');
                if (!empty($custom_message)) {
                    $message = str_replace(
                        ['@seller_name', '@seller_email'],
                        [$seller_user->name, $seller_user->email],
                        $custom_message
                    );
                } else {
                    $message = '
                        <p>Hello Admin,</p>
                        <p>A seller has submitted a <strong>verification request</strong> and is waiting for your review.</p>
                        <table style="border-collapse:collapse; width:100%; margin-top:15px;">
                            <tr style="background:#f9f9f9;">
                                <td style="padding:10px; border:1px solid #ddd; font-weight:bold;">Seller Name</td>
                                <td style="padding:10px; border:1px solid #ddd;">' . e($seller_user->name) . '</td>
                            </tr>
                            <tr>
                                <td style="padding:10px; border:1px solid #ddd; font-weight:bold;">Seller Email</td>
                                <td style="padding:10px; border:1px solid #ddd;">' . e($seller_user->email) . '</td>
                            </tr>
                        </table>
                        <p style="margin-top:20px;">
                            <a href="' . $admin_verify_url . '" style="background-color:#007bff; color:#fff; padding:10px 20px; border-radius:5px; text-decoration:none; display:inline-block;">
                                Review Verification Request
                            </a>
                        </p>
                        <p>Please log in to the admin dashboard to review and approve or reject this request.</p>
                    ';
                }

                Mail::to(get_static_option('site_global_email'))->queue(new BasicMail([
                    'subject' => get_static_option('seller_verification_subject') ?? __('New Seller Verification Request'),
                    'message' => $message
                ]));
            } catch (\Exception $e) {
                return redirect()->back()->with(FlashMsg::item_new($e->getMessage()));
            }

            toastr_success(__('Verify Info Update Success---'));
            return redirect()->back();
        }
        $seller_verify_info = SellerVerify::where('seller_id', $user)->first();
        return view('frontend.user.seller.profile-verify.seller-profile-verify', compact('seller_verify_info'));
    }

    /* Extra Service Request */
    public function extraService(Request $request)
    {

        $request->validate([
            'order_id' => 'required|integer',
            'title' => 'required|max:191',
            'quantity' => 'required|integer|gte:0',
            'price' => 'required',
        ]);



        //todo: get order details from database
        $orderDetails = Order::find($request->order_id);
        //todo: check order payment status paid or completed
        if ($orderDetails->payment_status === 'complete') {
            //todo: if order status is completed then save data in new database table , update order table total price and admin commission etc
            $commission_charge = $orderDetails->commission_charge;
            $commission_type = $orderDetails->commission_type;

            //todo: add new additional service in database
            $additional_service_cost =  $request->price * $request->quantity;
            //todo calculate admin commission
            $commission_amount = ServiceCalculationHelper::calculateCommission($commission_type, $commission_charge, $additional_service_cost, $orderDetails->seller_id);;
            //todo get sub total
            $sub_total = $additional_service_cost;
            //todo calculate tax
            $service_details_for_book = Service::select('id', 'service_city_id')->where('id', $orderDetails->service_id)->first();
            if (!empty($service_details_for_book)) {
                $service_country =  optional(optional($service_details_for_book->serviceCity)->countryy)->id;
                //todo: update tax amount
                $tax =  ServiceCalculationHelper::calculateTax($additional_service_cost, $service_country);
                $total = $additional_service_cost + $tax;
                //todo get total
                ExtraService::create([
                    'order_id' => $orderDetails->id,
                    'title' => $request->title,
                    'price' => $request->price,
                    'quantity' => $request->quantity,
                    'tax' => $tax,
                    'commission_amount' => $commission_amount,
                    'sub_total' => $sub_total,
                    'total' => $total,
                    'payment_status' => 'pending',
                    'status' => 0
                ]);

                try {
                    //send mail to seller
                    $seller_details = User::select('name', 'email')->find($orderDetails->seller_id);
                    $message = get_static_option('seller_extra_service_message');
                    $message = str_replace(["@seller_name", "@order_id"], [$seller_details->name, $orderDetails->id], $message);
                    Mail::to($seller_details->email)->queue(new BasicMail([
                        'subject' => get_static_option('seller_extra_service_subject') ?? __('Extra Service Added'),
                        'message' => $message
                    ]));

                    $smsService = new SMSService();
                    $seller_id = $orderDetails->seller_id;
                    $message_for_seller_admin = __('your have added extra service in your order #') . $orderDetails->id;

                    $seller_phone = User::select('phone')->where('id', $seller_id)->first();
                    //send sms to seller
                    $seller_phone=$seller_phone->phone;
                    $smsService->send_sms($seller_phone,  $message_for_seller_admin);



                    //msService->send_sms($number,  $message_for_seller_admin);

                    $buyer_details = User::select('name', 'email','phone')->find($orderDetails->buyer_id);
                    //send mail to buyer
                    $message = get_static_option('seller_to_buyer_extra_service_message');
                    $message = str_replace(["@buyer_name", "@order_id"], [$buyer_details->name, $orderDetails->id], $message);
                    Mail::to($buyer_details->email)->queue(new BasicMail([
                        'subject' => get_static_option('seller_extra_service_subject') ?? __('Extra Service Added'),
                        'message' => $message
                    ]));
                    //send sms to buyer
                    $message_for_buyer = __('seller added extra service in your order #') . $orderDetails->id;

                    $buyer_phone = $buyer_details->phone;

                    //send sms to buyer
                    $smsService->send_sms($buyer_phone,  $message_for_buyer);



                    // $smsService->send_sms($number,  $message_for_buyer);

                    $admins = Admin::all();
                    foreach ($admins as $admin) {
                        if ($admin->role == "Super Admin") {
                            $seller_name = User::select('name')->where('id', $seller_id)->first();
                            $seller_name = $seller_name ? $seller_name->name : 'Unknown Seller';
                            $message_for_super_admin = get_static_option('extra_order_super_admin_message') ?? __('Seller added extra service in order #');
                            $message_for_super_admin = str_replace(
                                ['{{order_id}}', '{{seller_name}}'],
                                [$orderDetails->id, $seller_name],
                                $message_for_super_admin
                            );

                            $smsService->send_sms($admin->phone,  $message_for_super_admin);
                            // $smsService->send_sms($number,  $message_for_super_admin);
                        }
                    }

                    // 🔔 Send push notification to buyer
                    $buyer = $orderDetails->buyer;
                    if ($buyer) {
                        $pushTitle = __('Extra Service Requested');
                        $pushBody  = __('Seller has requested extra service for Order #:id', [
                            'id' => $orderDetails->id
                        ]);

                        $extraData = [
                            'order_id' => $orderDetails->id,
                            'seller_id'=> $orderDetails->seller_id,
                            'buyer_id' => $buyer->id,
                            'type'     => 'ExtraServiceRequest',
                        ];

                        send_push_notification(
                            $buyer,        // notifiable
                            $orderDetails->id,    // identity
                            '', // type (or create 'OrderCompletion' if you prefer)
                            $pushBody,     // body
                            $pushTitle,    // title
                            $extraData,
                            $orderDetails,
                            true
                        );
                    }

                    // 🔔 Send push notification to seller
                    $seller = $orderDetails->seller;
                    if ($seller) {
                        $pushTitle = __('Extra Service Requested');
                        $pushBody  = __('You have added extra service for Order #:id', [
                            'id' => $orderDetails->id
                        ]);

                        $extraData = [
                            'order_id' => $orderDetails->id,
                            'seller_id'=> $orderDetails->seller_id,
                            'buyer_id' => $seller->id,
                            'type'     => 'ExtraServiceRequest',
                        ];

                        send_push_notification(
                            $seller,        // notifiable
                            $orderDetails->id,    // identity
                            '', // type (or create 'OrderCompletion' if you prefer)
                            $pushBody,     // body
                            $pushTitle,    // title
                            $extraData,
                            $orderDetails,
                            true
                        );
                    }
                } catch (\Exception $e) {
                    //handle error
                }

                toastr_success(__('Extra Service Request Send'));
                return back();
            } else {
                toastr_error(__('Service Not Found'));
                return back();
            }
        } else {
            $commission_charge = $orderDetails->commission_charge;
            $commission_type = $orderDetails->commission_type;

            //todo: add new additional service in database
            $additional_service_cost =  $request->price * $request->quantity;
            OrderAdditional::create([
                'order_id' => $orderDetails->id,
                'title' => $request->title,
                'price' => $request->price,
                'quantity' => $request->quantity,
            ]);

            //todo: update extra_service [extra service price * quantity]
            $orderDetails->extra_service += $additional_service_cost;


            //todo: update commission
            $orderDetails->commission_amount += ServiceCalculationHelper::calculateCommission($commission_type, $commission_charge, $additional_service_cost, $orderDetails->seller_id); //$commission_amount;
            //todo: update sub_total []
            $orderDetails->sub_total += $additional_service_cost;
            $new_sub_total =  $orderDetails->sub_total  + $additional_service_cost;

            //todo: calculate tax []
            $total = 0;
            $tax_amount = 0;

            $service_details_for_book = Service::select('id', 'service_city_id')->where('id', $orderDetails->service_id)->first();
            if (!empty($service_details_for_book)) {
                $service_country =  optional(optional($service_details_for_book->serviceCity)->countryy)->id;
                //todo: update tax amount
                $orderDetails->tax +=  ServiceCalculationHelper::calculateTax($new_sub_total, $service_country); //$tax_amount;
                //todo: update total amount []
                $total = $additional_service_cost + $tax_amount;
                $orderDetails->total += $total;
                $orderDetails->save();
                //todo send mail to seller and buyer
                try {
                    //send mail to seller
                    $seller_details = User::select('name', 'email')->find($orderDetails->seller_id);
                    $message = '<p>';
                    $message .= __('Hello') . ' ' . $seller_details->name . ',' . "<br>";
                    $message .= __('your have added extra service in your order #') . $orderDetails->id;
                    $message .= '</p>';
                    Mail::to($seller_details->email)->queue(new BasicMail([
                        'subject' => __('Extra service added in your order #') . $orderDetails->id,
                        'message' => $message
                    ]));
                    $smsService = new SMSService();

                    $seller_id = $orderDetails->seller_id;
                    $message_for_seller_admin = __('your have added extra service in your order #') . $orderDetails->id;

                    $seller_phone = User::select('phone')->where('id', $seller_id)->first();
                    $seller_phone = $seller_phone->phone;

                    //send sms to seller
                    $smsService->send_sms($seller_phone,  $message_for_seller_admin);



                    //  $smsService->send_sms($number,  $message_for_seller_admin);

                    $buyer_details = User::select('name', 'email')->find($orderDetails->buyer_id);
                    //send mail to buyer
                    $message = '<p>';
                    $message .= __('Hello') . ' ' . $buyer_details->name . ',' . "<br>";
                    $message .= __('seller added extra service in your order #') . $orderDetails->id;
                    $message .= '</p>';
                    Mail::to($buyer_details->email)->queue(new BasicMail([
                        'subject' => __('Extra service added in your order #') . $orderDetails->id,
                        'message' => $message
                    ]));

                    //send sms to buyer
                    $message_for_buyer = __('seller added extra service in your order #') . $orderDetails->id;

                    $buyer_phone = User::select('phone')->where('id', $orderDetails->buyer_id)->first();

                    //send sms to buyer
                    $smsService->send_sms($buyer_phone->phone,  $message_for_buyer);

                    //    $smsService->send_sms($number,  $message_for_buyer);
                    $admins = Admin::all();
                    foreach ($admins as $admin) {
                        if ($admin->role == "Super Admin") {
                            $seller_name = User::select('name')->where('id', $seller_id)->first();
                            $seller_name = $seller_name ? $seller_name->name : 'Unknown Seller';
                            $message_for_super_admin = get_static_option('new_order_super_admin_message') ?? __('Seller have a new order #');
                            $message_for_super_admin = str_replace(
                                ['{{order_id}}', '{{seller_name}}'],
                                [$orderDetails->id, $seller_name],
                                $message_for_super_admin
                            );

                            $smsService->send_sms($admin->phone,  $message_for_super_admin);
                            // $smsService->send_sms($number,  $message_for_super_admin);
                        }
                    }

                    // 🔔 Send push notification to buyer
                    $buyer = $orderDetails->buyer;
                    if ($buyer) {
                        $pushTitle = __('Extra Service Requested');
                        $pushBody  = __('Seller has requested extra service for Order #:id', [
                            'id' => $orderDetails->id
                        ]);

                        $extraData = [
                            'order_id' => $orderDetails->id,
                            'seller_id'=> $orderDetails->seller_id,
                            'buyer_id' => $buyer->id,
                            'type'     => 'ExtraServiceRequest',
                        ];

                        send_push_notification(
                            $buyer,        // notifiable
                            $orderDetails->id,    // identity
                            '', // type (or create 'OrderCompletion' if you prefer)
                            $pushBody,     // body
                            $pushTitle,    // title
                            $extraData,
                            $orderDetails,
                            true
                        );
                    }

                    // 🔔 Send push notification to seller
                    $seller = $orderDetails->seller;
                    if ($seller) {
                        $pushTitle = __('Extra Service Requested');
                        $pushBody  = __('You have added extra service for Order #:id', [
                            'id' => $orderDetails->id
                        ]);

                        $extraData = [
                            'order_id' => $orderDetails->id,
                            'seller_id'=> $orderDetails->seller_id,
                            'buyer_id' => $seller->id,
                            'type'     => 'ExtraServiceRequest',
                        ];

                        send_push_notification(
                            $seller,        // notifiable
                            $orderDetails->id,    // identity
                            '', // type (or create 'OrderCompletion' if you prefer)
                            $pushBody,     // body
                            $pushTitle,    // title
                            $extraData,
                            $orderDetails,
                            true
                        );
                    }
                } catch (\Exception $e) {
                    //handle error
                }
               

                toastr_success(__('Extra Service Request Send'));
                return back();
            } else {
                toastr_error(__('Service Not Found'));
                return back();
            }
        }
        //todo: else add it in order_additional table and update order table total price and admin commission etc
        toastr_error(__('something went wrong, try after sometime'));
        return back();
    }


    public function extraServiceDelete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        ExtraService::find($request->id)->delete();

        return response([
            'msg' => __('Delete Success')
        ]);
    }

    public function orderRequestDeclineHistory($id)
    {
        $order_id = $id;
        $decline_histories = OrderCompleteDecline::latest()->where('order_id', $id)->paginate(10);
        return view('frontend.user.seller.order.decline-history', compact('decline_histories', 'order_id'));
    }

    // seller to buyer review
    public function sellerToBuyerReview(Request $request)
    {
        $request->validate([
            'rating' => 'required',
            'message' => 'required',
        ]);

        $review_count = Review::where('order_id', $request->order_id)->where('type', 0)->where('seller_id', Auth::guard('web')->user()->id)->first();
        if (!$review_count) {
            $review = Review::create([
                'order_id' => $request->order_id,
                'service_id' => $request->service_id ?? 0,
                'buyer_id' => $request->buyer_id,
                'seller_id' => Auth::guard()->check() ? Auth::guard('web')->user()->id : NULL,
                'rating' => $request->rating,
                'name' => Auth::guard()->check() ? Auth::guard('web')->user()->name : NULL,
                'email' => Auth::guard()->check() ? Auth::guard('web')->user()->email : NULL,
                'message' => $request->message,
                'type' => 0,
            ]);
            if ($review) {
                toastr_success(__('Review Added Success---'));
                return redirect()->back();
            }
        }
        toastr_error(__('You Can Not Send Review More Than One'));
        return redirect()->back();
    }

    public function createTicket(Request $request)
    {
        $seller_id = Auth::guard('sanctum')->user()->id;

        if ($request->order_id) {
            $buyer_id = Order::select('buyer_id')->where('id', $request->order_id)->first();
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:191',
            'subject' => 'required|string|max:191',
            'priority' => 'required|string|max:191',
            'description' => 'required|string',
            'order_id' => 'required|string'
        ], [
            'title.required' => __('title required'),
            'subject.required' =>  __('subject required'),
            'priority.required' =>  __('priority required'),
            'description.required' => __('description required'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()
            ], 422);
        }

        SupportTicket::create([
            'title' => $request->title,
            'description' => $request->description,
            'subject' => $request->subject,
            'status' => 'open',
            'priority' => $request->priority,
            'seller_id' => $seller_id,
            'buyer_id' => $buyer_id->buyer_id,
            'order_id' => $request->order_id,
        ]);
        // toastr_success(__('Ticket successfully created.'));
        $last_ticket_id = DB::getPdo()->lastInsertId();
        $last_ticket = SupportTicket::where('id', $last_ticket_id)->first();

        // send order ticket notification to buyer
        $buyer = User::where('id', $last_ticket->buyer_id)->first();
        if ($buyer) {
            $order_ticcket_message = __('You have a new order ticket');
            $buyer->notify(new TicketNotification($last_ticket_id, $seller_id, $last_ticket->buyer_id, $order_ticcket_message));
        }
        // admin notification add
        AdminNotification::create(['ticket_id' => $last_ticket_id]);

        //Send ticket mail to buyer and admin
        try {
            $message = get_static_option('seller_order_ticket_message');
            $message = str_replace(["@order_ticket_id"], [$last_ticket_id], $message);
            Mail::to(get_static_option('site_global_email'))->queue(new BasicMail([
                'subject' => get_static_option('order_ticket_subject') ?? __('New Order Ticket'),
                'message' => $message
            ]));
            Mail::to($buyer->email)->queue(new BasicMail([
                'subject' => get_static_option('seller_order_ticket_subject') ?? __('New Order Ticket'),
                'message' => $message
            ]));
        } catch (\Exception $e) {
            //return redirect()->back()->with(FlashMsg::item_new($e->getMessage()));
        }

        return response()->success([
            "ticket" => "",
            "message" => __('Ticket successfully created.')
        ]);
    }

    public function allClearMessage(Request $request)
    {
        if (Auth::guard('web')->user()->unreadNotifications->count() >= 1) {
            Auth::guard('web')->user()->Notifications->markAsRead();
            toastr_success(__('Clear all Notifications Success---'));
        } else {
            toastr_error(__('No Notifications Found'));
        }
        return redirect()->back();
    }

    private function translateFlatServicePayload(GoogleTranslateService $translator, array $data): array
    {
        return $translator->translatePayload($data, [
            'title' => 'text',
            'description' => 'html',
            'meta_title' => 'text',
            'meta_description' => 'text',
            'facebook_meta_tags' => 'text',
            'facebook_meta_description' => 'text',
            'twitter_meta_tags' => 'text',
            'twitter_meta_description' => 'text',
            'include_service_title.*' => 'text',
            'additional_service_title.*' => 'text',
            'benifits.*' => 'text',
            'faqs_title.*' => 'text',
            'faqs_description.*' => 'text',
        ]);
    }

    private function translateNestedServicePayload(GoogleTranslateService $translator, array $data): array
    {
        return $translator->translatePayload($data, [
            'title' => 'text',
            'description' => 'html',
            'meta_title' => 'text',
            'meta_description' => 'text',
            'facebook_meta_tags' => 'text',
            'facebook_meta_description' => 'text',
            'twitter_meta_tags' => 'text',
            'twitter_meta_description' => 'text',
            'include_service_inputs.*.include_service_title' => 'text',
            'additional_service_inputs.*.additional_service_title' => 'text',
            'service_benefit_inputs.*.benifits' => 'text',
            'online_service_faq.*.title' => 'text',
            'online_service_faq.*.description' => 'text',
        ]);
    }

    private function validateTranslatedServiceTitle(string $title, ?int $ignoreServiceId = null): void
    {
        $titleRule = Rule::unique('services', 'title');

        if ($ignoreServiceId !== null) {
            $titleRule->ignore($ignoreServiceId);
        }

        Validator::make(
            ['title' => $title],
            ['title' => ['required', 'max:191', $titleRule]]
        )->validate();
    }

    private function resolveServiceSlug(?string $originalTitle, ?string $translatedTitle, ?string $requestedSlug): string
    {
        $normalizedRequestedSlug = Str::slug((string) $requestedSlug);
        $normalizedOriginalTitle = Str::slug((string) $originalTitle);

        if ($normalizedRequestedSlug !== '' && $normalizedRequestedSlug !== $normalizedOriginalTitle) {
            return $normalizedRequestedSlug;
        }

        return Str::slug((string) ($translatedTitle ?: $originalTitle ?: $requestedSlug));
    }
}
