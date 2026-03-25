<?php

namespace Modules\WhatsAppBookingSystem\Http;

use App\AdminCommission;
use App\AdminNotification;
use App\Day;
use App\Mail\OrderMail;
use App\Order;
use App\OrderAdditional;
use App\OrderInclude;
use App\Service;
use App\Serviceadditional;
use App\Notifications\OrderNotification;
use App\Schedule;
use App\ServiceCoupon;
use App\Serviceinclude;
use App\SupportTicket;
use App\Tax;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Modules\Wallet\Entities\Wallet;
use Modules\WhatsAppBookingSystem\Entities\WhatsAppBooking;
use Modules\WhatsAppBookingSystem\Entities\WhatsAppBookingAddon;
use Modules\WhatsAppBookingSystem\Entities\WhatsAppBookingInclude;

class ServiceApiClient
{

    public function searchServices($keyword)
    {
        $url = route('whatsapp.services.search', ['keyword' => $keyword]);
        return Http::get($url)->json();
    }

    public function getServiceAddons($service_id)
    {
        $url = route('whatsapp.services.addons', ['id' => $service_id]);
        return Http::get($url)->json();
    }

    public function getServiceIncludesList($service_id)
    {
        $url = route('whatsapp.services.includes', ['id' => $service_id]);
        return Http::get($url)->json();
    }

    public function getServiceDetails($id,$phone)
    {
        $url = route('whatsapp.services.details', ['id' => $id]);
        return Http::get($url)->json();
    }

    public function getServiceInclude($phone,$id)
    {
        $url = route('whatsapp.services.includes.addons', ['id' => $id]);
        return Http::get($url)->json();
    }

    public function getServiceFaq($phone,$id)
    {
        $url = route('whatsapp.services.faqs', ['id' => $id]);
        return Http::get($url)->json();
    }

    public function getRecentOrderDetails($phone)
    {
        $url = route('whatsapp.user.recent-order.details', ['phone' => $phone]);
        return Http::get($url)->json();
    }

    public function getUserByPhone($phone)
    {
        $url=route('whatsapp.user.by-phone',['phone'=>$phone]);
        return Http::get($url)->json();
    }

    public function getStaffList($serviceId)
    {
        $url = route('whatsapp.service.staff-lists', ['service_id'=>$serviceId]);
        return Http::get($url)->json();
    }


    public function getLocationList($clientId)
    {
        $url = route('whatsapp.client.location-lists', ['client_id'=>$clientId]);
        return Http::get($url)->json();
    }

    public function getAvailableSlots($phone,$serviceId,$date)
    {
        $url = url("/api/v1/whatsapp/available-slots/$phone/$serviceId/$date");
        return Http::get($url)->json();

    }

    public function getOrderServiceDetails($phone)
    {
        $url = route('whatsapp.order.service.details', ['phone' => $phone]);
        return Http::get($url)->json();
    }

    public function getOrderAddonDetails($phone)
    {
        $url = route('whatsapp.order.addons.details', ['phone' => $phone]);
        return Http::get($url)->json();
    }

    public function getOrderOtherDetails($phone)
    {
        $url = route('whatsapp.order.other.details', ['phone' => $phone]);
        return Http::get($url)->json();
    }


    public function placeOrder($serviceId, $phone)
    {
        $buyer=User::where('phone', $phone)->first();
        $buyer_id=$buyer->id;
        $data=WhatsAppBooking::where('buyer_id', $buyer_id)->first();
        $service= Service::find($serviceId);

       if($service->is_service_online === 0) {
           if (!$data->address) {
               return ['error' => 'You have to give your address'];
           } else if (!$data->date) {
               return ['error' => 'Kindly provide a date on which you would like to receive the service.'];
           } else if (!$data->schedule) {
               return ['error' => 'You have to give your schedule'];
           }
       }

       $commission = AdminCommission::first();
       $payment_status='pending';

       $order_create='';
       if($service->is_service_online != 1){
           Order::create([
               'service_id' => $serviceId,
               'seller_id' => $service->seller_id,
               'buyer_id' => $buyer_id,
               'name' => $buyer->name,
               'email' => $buyer->email,
               'phone' => $phone,
               'address' => $data->address,
               'date' => \Carbon\Carbon::parse($data->date)->format('D F d Y'),
               'schedule' => $data->schedule,
               'package_fee' => 0,
               'extra_service' => 0,
               'sub_total' => 0,
               'tax' => 0,
               'total' => 0,
               'commission_type' => $commission->commission_charge_type,
               'commission_charge' => $commission->commission_charge,
               'status' => 0,
               'payment_gateway' => "cash_on_delivery",
               'payment_status' => $payment_status,
           ]);
       }else{

           $order_create = Order::create([
               'service_id' => $serviceId,
               'seller_id' => $service->seller_id,
               'buyer_id' => $buyer_id,
               'name' => $buyer->name,
               'email' => $buyer->email,
               'phone' => $phone,
               'package_fee' => 0,
               'extra_service' => 0,
               'sub_total' => 0,
               'tax' => 0,
               'total' => 0,
               'commission_type' => $commission->commission_charge_type,
               'commission_charge' => $commission->commission_charge,
               'status' => 0,
               'is_order_online'=>$service->is_service_online,
               'payment_gateway' => "cash_on_delivery",
               'payment_status' => $payment_status,
           ]);
       }

       $last_order_id = DB::getPdo()->lastInsertId();

       // invoice generate
       $invoiceNumber = 'INV'.$last_order_id;
       Order::where('id', $last_order_id)->update(['invoice' => $invoiceNumber]);

       if($order_create !=''){
           SupportTicket::create([
               'title' => 'New Order',
               'subject' => 'Order Created By'.$buyer->name,
               'status' => 'open',
               'priority' => 'high',
               'buyer_id' => $buyer_id,
               'seller_id' => $service->seller_id,
               'service_id' => $serviceId,
               'order_id' => $last_order_id ,
           ]);
       }

       $service_sold_count = Service::select('sold_count')->where('id',$serviceId)->first();
       Service::where('id',$serviceId)->update(['sold_count'=>$service_sold_count->sold_count+1]);

       $servs = [];
       $service_ids = [];
       $package_fee = 0;

       $included_services = Serviceinclude::whereIn('id', $service_ids)->get();

       if($service->is_service_online != 1) {
           $booking_includes= WhatsAppBookingInclude::where('whats_app_booking_id', $data->id)->get();

           if($booking_includes->isNotEmpty()){

               foreach ($booking_includes as $include) {
                   $included_service = Serviceinclude::where('id', $include->include_id)->first();

                   $package_fee += $include->quantity *  $included_service->include_service_price;

                      OrderInclude::create([
                        'order_id' => $last_order_id,
                        'title' => $included_service->include_service_title,
                        'price' => $included_service->include_service_price,
                        'quantity' => $include->quantity,
                      ]);

               }
           } else if($booking_includes->isEmpty()){


               $included_service = Serviceinclude::where('service_id', $serviceId)->get();

                 foreach ($included_service as $include) {
                      $package_fee += $include->include_service_price;

                      OrderInclude::create([
                        'order_id' => $last_order_id,
                        'title' => $include->include_service_title,
                        'price' => $include->include_service_price,
                        'quantity' => 1,
                      ]);
                 }


           }
       }else{
           $package_fee = $service->price;
       }


       $extra_service = 0;

       $booking_additional= WhatsAppBookingAddon::where('whats_app_booking_id', $data->id)->get();

       if($booking_additional->isNotEmpty()){

           foreach ($booking_additional as $addon) {
               $additional_service = Serviceadditional::where('id', $addon->addon_id)->first();

               $extra_service += $addon->quantity * $additional_service->additional_service_price;

           }
       }


       $sub_total = 0;
       $total = 0;
       $tax_amount =0;

       $service_details_for_book = Service::select('id','service_city_id')->where('id',$serviceId)->first();
       $service_country =  optional(optional($service_details_for_book->serviceCity)->countryy)->id;
       $country_tax =  Tax::select('id','tax')->where('country_id',$service_country)->first();
       $sub_total = $package_fee + $extra_service;
       if(!is_null($country_tax )){
           $tax_amount = ($sub_total * $country_tax->tax) / 100;
       }
       $total = $sub_total + $tax_amount;


       $commission_amount = 0;

       //commission amount
       if($commission->system_type == 'subscription'){
           if(subscriptionModuleExistsAndEnable('Subscription')){
               $commission_amount = 0;
               \Modules\Subscription\Entities\SellerSubscription::where('seller_id', $service->seller_id)->update([
                   'connect' => DB::raw(sprintf("connect - %s",(int)strip_tags(get_static_option('set_number_of_connect')))),
               ]);
           }
       }else{
           if($commission->commission_charge_type=='percentage'){
               $commission_amount = ($sub_total*$commission->commission_charge)/100;
           }else{
               $commission_amount = $commission->commission_charge;
           }
       }

       Order::where('id', $last_order_id)->update([
           'package_fee' => $package_fee,
           'extra_service' => $extra_service,
           'sub_total' => $sub_total,
           'tax' => $tax_amount,
           'total' => $total,
           'commission_amount' => $commission_amount,
       ]);

       //Send order notification to seller
       $seller = User::where('id', $service->seller_id)->first();
       $order_message = __('You have a new order');

       // admin notification add
       AdminNotification::create(['order_id' => $last_order_id]);

       // seller buyer notification
       $seller->notify(new OrderNotification($last_order_id, $serviceId, $service->seller_id, $buyer_id, $order_message));

       $order_details= Order::where('id', $last_order_id)->first();

        return [
            'order_id'      => $order_details->id,
            'service_title' =>  $order_details->service?->title ?? 'N/A',
            'service_type' => $order_details->service?->is_service_online ?? 'N/A',
            'price'         => $order_details->total ?? 'N/A',
            'status'        => $order_details->status ?? 'N/A',
            'payment_gateway' => $order_details->payment_gateway ?? 'N/A',
            'payment_status' => $order_details->payment_status ?? 'N/A',
        ];

    }

}
