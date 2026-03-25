<?php

namespace Modules\WhatsAppBookingSystem\Http\Controllers\Api;

use App\Day;
use App\Http\Controllers\Controller;
use App\Order;
use App\OrderInclude;
use App\Schedule;
use App\Serviceadditional;
use App\OrderAdditional;
use App\Service;
use App\Serviceinclude;
use App\Tax;
use App\User;
use Modules\WhatsAppBookingSystem\Entities\WhatsAppBooking;
use Modules\WhatsAppBookingSystem\Entities\WhatsAppBookingAddon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Modules\WhatsAppBookingSystem\Entities\WhatsAppBookingInclude;

class WhatsAppFunctionController extends Controller
{

    public function searchService(Request $request,$keyword)
    {
        //return top 10 service
        $keyword = trim($keyword);
        $services=Service::where('title', 'like', "%$keyword%")->where('status', 1)->where('is_service_on', 1)
            ->get(['id', 'title', 'price']);
        if ($services->isEmpty()) {
            return response()->json(['error' => 'No services found'], 404);
        }
        return response()->json(['data' => $services]);
    }

    public function getServiceAddons(Request $request, $id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['error' => 'Service not found'], 404);
        }
        $addons = $service->serviceAdditional;
        return response()->json($addons);
    }

    public function getServiceIncludes(Request $request, $id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['error' => 'Service not found'], 404);
        }
        $includes = $service->serviceInclude;
        return response()->json($includes);
    }

    public function getUserByPhone(Request $request, $phone)
    {
        return User::where('phone', $phone)->first();
    }

    public function serviceDetails(Request $request, $id)
    {
        $service = Service::find($id);
        $result='Offline';
        if( $service->is_service_online === 1)
        {
            $result='Online';
        }

        return response()->json([
            'id' => $service->id,
            'title' => $service->title,
            'category' => $service->category?->name ?? 'N/A',
            'price' => $service->price,
            'service_type' => $result,
        ]);
    }
    public function serviceInclude(Request $request, $id)
    {
        $service = Service::with(['serviceInclude','serviceAdditional'])->find($id);
        return response()->json([
            'included' => $service->serviceInclude->pluck('include_service_title')->toArray(),
            'excluded' => $service->serviceAdditional->pluck('additional_service_title')->toArray(),
            'service_id' => $service->id,
            'service_type' => $service->is_service_online == 1 ? 'Online' : 'Offline',
        ]);
    }

    public function serviceFaq(Request $request, $id)
    {
        $service = Service::with(['serviceFaq','serviceBenifit'])->find($id);

        if($service->is_service_online == 1)
        {
            return response()->json([
                'service_id' => $service->id,
                'service_type' => 'Online',
                'faqs' => $service->serviceFaq?->map(function ($faq) {
                    return [
                        'title' => $faq->title,
                        'description' => $faq->description,
                    ];
                })->toArray(),
                'benifits' => $service->serviceBenifit?->map(function ($benifit) {
                    return [
                        'title' => $benifit->benifits,
                    ];
                })->toArray(),


            ]);
        }
        else{
            return response()->json([
                'service_id' => $service->id,
                'service_type' => 'Offline',
                'benifits' => $service->serviceBenifit?->map(function ($benifit) {
                    return [
                        'title' => $benifit->benifits,
                    ];
                })->toArray(),
            ]);
        }

    }


    public function recentOrderDetails(Request $request,$phone)
    {
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            return ['error' => 'User not found'];
        }

        $recentOrder = Order::where('buyer_id',$user->id)->latest()->first();
        if (!$recentOrder) {
            return ['error' => 'No recent orders found'];
        }

        return [
            'order_id'      => $recentOrder->id,
            'service_title' => $recentOrder->service->title ?? 'N/A',
            'price'         => $recentOrder->total ?? 'N/A',
            'status'        => $recentOrder->status ?? 'N/A',
            'payment_gateway' => $recentOrder->payment_gateway ?? 'N/A',
            'payment_status' => $recentOrder->payment_status ?? 'N/A',
            'booked_at'    => $recentOrder->date,
        ];
    }

    public function orderServiceDetails(Request $request,$phone)
    {
        $client=User::where('phone', $phone)->first();
        $booking = WhatsAppBooking::where('buyer_id', $client->id)->first();
        $service= Service::find($booking->service_id);
        $includes_services = WhatsAppBookingInclude::where('whats_app_booking_id', $booking->id)->get();
        $includes=[];
        if ($includes_services->isNotEmpty()) {

            foreach ($includes_services as $include) {
                $include_service = Serviceinclude::where('service_id',  $booking->service_id)->where('id', $include->include_id)->first();
                $include_subtotal= $include->quantity * $include_service->include_service_price;
                $includes[]=[
                    'title' => mb_strimwidth($include_service?->include_service_title, 0, 24, '…'),
                    'quantity' => $include->quantity,
                    'total' => $include_subtotal,
                ];

            }

        }

        return [
            'service_title' => mb_strimwidth($service?->title, 0, 24, '…'),
            'includes' => $includes
        ];

    }

    public function orderAddonDetails(Request $request,$phone)
    {

        $client=User::where('phone', $phone)->first();
        $booking = WhatsAppBooking::where('buyer_id', $client->id)->first();
        $service= Service::find($booking->service_id);
        $addons_services = WhatsAppBookingAddon::where('whats_app_booking_id', $booking->id)->get();
        $addons=[];
        if ($addons_services->isNotEmpty()) {

            foreach ($addons_services as $addon) {
                $addon_service = Serviceadditional::where('service_id',  $booking->service_id)->where('id', $addon->addon_id)->first();
                $addon_subtotal= $addon->quantity * $addon_service->additional_service_price;
                $addons[]=[
                    'title' => mb_strimwidth($addon_service?->	additional_service_title, 0, 24, '…'),
                    'quantity' => $addon->quantity,
                    'total' => $addon_subtotal,
                ];

            }

        }

        return [
            'service_title' => mb_strimwidth($service?->title, 0, 24, '…'),
            'addons' => $addons,
        ];
    }

    public function orderOtherDetails(Request $request,$phone)
    {
        $client=User::where('phone', $phone)->first();
        $booking = WhatsAppBooking::where('buyer_id', $client->id)->first();
        $service= Service::find($booking->service_id);
        $booking_includes= WhatsAppBookingInclude::where('whats_app_booking_id', $booking->id)->get();
        $package_fee = 0;

        if($service->is_service_online_ != 1 && $booking_includes->isNotEmpty()){

            foreach ($booking_includes as $include) {
                $included_service = Serviceinclude::where('id', $include->include_id)->first();

                $package_fee += $include->quantity *  $included_service->include_service_price;

            }
        }else{

            $package_fee = $service->price;
        }

        $extra_service = 0;

        $booking_additional= WhatsAppBookingAddon::where('whats_app_booking_id', $booking->id)->get();

        if($booking_additional->isNotEmpty()){

            foreach ($booking_additional as $addon) {
                $additional_service = Serviceadditional::where('id', $addon->addon_id)->first();

                $extra_service += $addon->quantity * $additional_service->additional_service_price;

            }
        }

        $sub_total = 0;
        $total = 0;
        $tax_amount =0;

        $tax = $service->tax;
        $service_details_for_book = Service::select('id','service_city_id')->where('id',$service->id)->first();
        $service_country =  optional(optional($service_details_for_book->serviceCity)->countryy)->id;
        $country_tax =  Tax::select('id','tax')->where('country_id',$service_country)->first();
        $sub_total = $package_fee + $extra_service;
        if(!is_null($country_tax )){
            $tax_amount = ($sub_total * $country_tax->tax) / 100;
        }
        $total = $sub_total + $tax_amount;

       if($service->is_service_online === 0)
       {
           return [
               'service_type' => $service->is_service_online == 1 ? 'Online' : 'Offline',
               'date' => $booking->date,
               'schedule' => $booking->schedule,
               'location' => mb_strimwidth($booking->address,0,24,'…'),
               'include_total' => $package_fee,
               'addon_total' => $extra_service,
               'sub_total' => $sub_total,
               'tax' => $tax_amount,
               'total' => $total,
               'payment_gateway' => $booking->payment_gateway ?? 'cash_on_delivery'
           ];
       }

        return [
            'service_type' => $service->is_service_online == 1 ? 'Online' : 'Offline',
            'addon_total' => $extra_service,
            'sub_total' => $sub_total,
            'tax' => $tax_amount,
            'total' => $total,
            'payment_gateway' => $booking->payment_gateway ?? 'cash_on_delivery'
        ];

    }

    public function getAvaliableSlots($phone, $serviceId, $date)
    {
        $service = Service::find($serviceId);
        $dayOfWeek = date('D', strtotime($date));

        if($service->seller_id)
        {
            $day=Day::where('day', $dayOfWeek)->where('seller_id',$service->seller_id)->first();
            if($day) {
                $all_slots = Schedule::where('seller_id', $service->seller_id)
                    ->where('day_id', $day->id)
                    ->pluck('schedule')
                    ->toArray();
            }
        }
        $bookedSlots = Order::where('service_id', $serviceId)
            ->whereDate('date', $date)
            ->pluck('schedule')
            ->toArray();

        $availableSlots = array_values(array_diff($all_slots, $bookedSlots));

        return $availableSlots;
    }



}
