<?php

namespace Modules\WhatsAppBookingSystem\Handlers;

use App\Service;
use App\Serviceadditional;
use App\Serviceinclude;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\WhatsAppBookingSystem\Entities\SessionLog;
use App\User;
use Modules\WhatsAppBookingSystem\Entities\WhatsAppBooking;
use Modules\WhatsAppBookingSystem\Entities\WhatsAppBookingAddon;
use Carbon\Carbon;
use Modules\WhatsAppBookingSystem\Entities\WhatsAppBookingInclude;
use Modules\WhatsAppBookingSystem\Http\ServiceApiClient;
use Modules\WhatsAppBookingSystem\Http\Services\WhatsAppService;

class MessageHandler
{
    protected $sessionManager;
    protected $whatsapp;
    protected $api;

    public function __construct()
    {
        $this->sessionManager = new SessionManager();
        $this->whatsapp = new WhatsAppService();
        $this->api = new ServiceApiClient();
    }

    public function handle($phone, $message)
    {
        $session = $this->sessionManager->get($phone);
        $client= User::where('phone', $phone)->first();
        if($client)
        {
            $whatsappOrder= WhatsAppBooking::where('buyer_id', $client->id)->first();
        }

        if($message)
        {
            // Handle interactive button
            if ($message['type'] === 'interactive') {
                $button = $message['interactive']['button_reply'] ?? null;
                $list = $message['interactive']['list_reply'] ?? null;

                //Search Service
                if ($button && $button['id'] === 'search_service') {

                    $this->deleteExitingInfo($phone);

                    $lastAction= $this->getLastAction($phone);
                    // check last action
                    if ($lastAction && $lastAction->action !== 'welcome_template' && $lastAction->action !== 'can_help_template') {
                        return;
                    }

                    $this->sessionManager->store($phone, [
                        'last_action' => 'awaiting_keyword'
                    ]);

                    $this->logSession($phone, 'awaiting_keyword');

                    $value=get_whatsapp_option('whatsapp_message_search_service');

                    $msg= __($value ?: 'Please type the service you are looking for.');

                    return $this->whatsapp->sendText($phone, $msg);
                }
                // Order Details
                else if ($button && $button['id'] === 'order_details') {

                    $this->deleteExitingInfo($phone);

                    $lastAction= $this->getLastAction($phone);
                    // check last action
                    if ($lastAction && $lastAction->action !== 'welcome_template' && $lastAction->action !== 'can_help_template') {
                        return;
                    }

                    $this->logSession($phone, 'clicked_order_details');

                    return $this->getRecentOrderDetails($phone);

                }
                else if( $button && $button['id'] === 'talk_to_support') {

                    $this->deleteExitingInfo($phone);
                    $lastAction= $this->getLastAction($phone);
                    // check last action
                    if ($lastAction && $lastAction->action !== 'welcome_template' && $lastAction->action !== 'can_help_template') {
                        return;
                    }

                    $this->logSession($phone, 'talk_to_support');
                    return $this->whatsapp->sendText($phone, "You can now chat with us normally.");
                }
                else if( $button && str_starts_with($button['id'], 'service_include_')) {

                    $this->deleteExitingInfo($phone);
                    $lastAction= $this->getLastAction($phone);

                    if($lastAction && ($lastAction->action !== 'service_details' && $lastAction->action !== 'service_faq' && $lastAction->action !== 'service_include')) {
                        $this->logSession($phone, 'welcome_template');
                        return $this->whatsapp->sendWelcomeTemplate($phone);
                    }


                    $service_id = str_replace('service_include_', '', $button['id']);
                    $this->logSession($phone, 'service_include');
                    $results = $this->api->getServiceInclude($phone,$service_id);
                    return $this->whatsapp->sendServiceIncludes($phone, $results);

                }
                else if( $button && str_starts_with($button['id'], 'service_faqs_')) {

                    $this->deleteExitingInfo($phone);
                    $lastAction= $this->getLastAction($phone);

                    if($lastAction && ($lastAction->action !== 'service_details' && $lastAction->action !== 'service_faq' && $lastAction->action !== 'service_include')) {
                        $this->logSession($phone, 'welcome_template');
                        return $this->whatsapp->sendWelcomeTemplate($phone);
                    }

                    $service_id = str_replace('service_faqs_', '', $button['id']);
                    $this->logSession($phone, 'service_faq');
                    $results = $this->api->getServiceFaq($phone,$service_id);
                    return $this->whatsapp->sendServiceFaqs($phone, $results);

                }
                else if($button && $button['id'] === 'order_addon_details')
                {
                    $lastAction= $this->getLastAction($phone);
                    // check last action
                    if($lastAction && ($lastAction->action !== 'order_other_details' && $lastAction->action !== 'select_slot_for_order' && $lastAction->action !== 'order_service' && $whatsappOrder ))
                    {
                        return $this->whatsapp->sendCancelOrder($phone);
                    }
                    else if($lastAction && ($lastAction->action !== 'order_other_details' && $lastAction->action !== 'select_slot_for_order' && $lastAction->action !== 'order_service' && !$whatsappOrder))
                    {
                        $this->logSession($phone, 'welcome_template');
                        return $this->whatsapp->sendWelcomeTemplate($phone);
                    }
                    $this->logSession($phone, 'order_addon_details');
                    $result= $this->api->getOrderAddonDetails($phone);
                    return $this->whatsapp->sendOrderAddonDetails($phone,$result);
                }
                else if($button && $button ['id'] === 'order_other_details')
                {
                    $lastAction= $this->getLastAction($phone);
                    // check last action
                    if($lastAction && ($lastAction->action !== 'order_addon_details' && $lastAction->action !== 'select_slot_for_order' && $lastAction->action !== 'order_service' && $whatsappOrder ))
                    {
                        return $this->whatsapp->sendCancelOrder($phone);
                    }
                    else if($lastAction && ($lastAction->action !== 'order_addon_details' && $lastAction->action !== 'select_slot_for_order' && $lastAction->action !== 'order_service' && !$whatsappOrder))
                    {
                        $this->logSession($phone, 'welcome_template');
                        return $this->whatsapp->sendWelcomeTemplate($phone);
                    }

                    $this->logSession($phone, 'order_other_details');
                    $result= $this->api->getOrderOtherDetails($phone);
                    return $this->whatsapp->sendOrderOtherDetails($phone,$result);
                }
                else if( $button && str_starts_with($button['id'], 'order_service_')) {

                    $lastAction= $this->getLastAction($phone);
                    // check last action
                    if($lastAction && $lastAction->action !== 'service_details' && $lastAction->action !== 'service_faq' && $lastAction->action !== 'service_include')
                    {
                        $this->logSession($phone, 'welcome_template');
                        return $this->whatsapp->sendWelcomeTemplate($phone);
                    }

                    $this->logSession($phone, 'order_service');
                   return $this->orderService($phone, $button['id']);

                }
                else if( $button && $button['id']==='cancel_order_yes') {

                    $client=User::where('phone', $phone)->first();
                    $bookingIds = WhatsAppBooking::where('buyer_id', $client->id)->pluck('id');
                    if ($bookingIds->isNotEmpty()) {
                        WhatsAppBookingInclude::whereIn('whats_app_booking_id', $bookingIds)->delete();
                        WhatsAppBookingAddon::whereIn('whats_app_booking_id', $bookingIds)->delete();
                        WhatsAppBooking::whereIn('id', $bookingIds)->delete();
                    }
                    $this->logSession($phone,'can_help_template');
                    return $this->whatsapp->sendConversationText($phone);

                }
                else if($button && $button['id'] === 'cancel_order_no') {
                    return $this->cancelOrderNo($phone);
                }
                else if($button && $button['id'] === 'confirm_order_yes') {
                    $lastAction= $this->getLastAction($phone);
                    // check last action
                    if($lastAction && ($lastAction->action !== 'order_other_details' && $lastAction->action !== 'order_addon_details')) {
                        $this->logSession($phone, 'welcome_template');
                        return $this->whatsapp->sendWelcomeTemplate($phone);
                    }
                   return $this->confirmOrderYes($phone);
                }
                else if($button && $button['id'] === 'confirm_order_no') {

                    $client=User::where('phone', $phone)->first();

                    $bookingIds = WhatsAppBooking::where('buyer_id', $client->id)->pluck('id');

                    if ($bookingIds->isNotEmpty()) {

                       WhatsAppBookingInclude::whereIn('whats_app_booking_id', $bookingIds)->delete();
                        WhatsAppBookingAddon::whereIn('whats_app_booking_id', $bookingIds)->delete();
                        WhatsAppBooking::whereIn('id', $bookingIds)->delete();
                    }
                    $this->logSession($phone,'can_help_template');
                    return $this->whatsapp->sendConversationText($phone);
                }


                //list section
                if ($list && str_starts_with($list['id'], 'view_more_after_')) {
                    $afterId = (int) str_replace('view_more_after_', '', $list['id']);

                    $results = $this->sessionManager->get($phone)['search_results'] ?? [];
                    //forget session
                    $this->sessionManager->forget($phone);

                    return $this->whatsapp->sendServiceList($phone, $results,$afterId);
                } // List selection (service_id)
                else if ($list && str_starts_with($list['id'], 'service_')) {
                    $id = str_replace('service_', '', $list['id']);
                    $title = $list['title'];
                    $this->logSession($phone, 'service_details');

                    $service_details=$this->api->getServiceDetails($id, $phone);
                    return $this->whatsapp->sendServiceDetails($phone, $service_details);

                }
                else if( $list && str_starts_with($list['id'], 'addon_qty_'))
                {
                    $lastAction= $this->getLastAction($phone);
                    // check last action
                    if($lastAction && ($lastAction->action !== 'addon_select_for_order' && $whatsappOrder))
                    {
                        return $this->whatsapp->sendCancelOrder($phone);
                    }
                    else if($lastAction && ($lastAction->action !== 'addon_select_for_order' && !$whatsappOrder))
                    {
                        $this->logSession($phone, 'welcome_template');
                        return $this->whatsapp->sendWelcomeTemplate($phone);
                    }
                    $rawId = str_replace('addon_qty_', '', $list['id']);
                    [$addonId, $quantity] = explode('_', $rawId);
                    $id = (int) $addonId;
                    $quantity = (int) $quantity;
                    $client=User::where('phone', $phone)->first();
                    $booking=WhatsAppBooking::where('buyer_id', $client->id)->first();
                    $addon=WhatsAppBookingAddon::where('whats_app_booking_id', $booking->id)
                        ->where('addon_id', $id)
                        ->first();

                    if(!$addon) {
                        return $this->whatsapp->sendText($phone, "Addon not found.");
                    }

                    $this->logSession($phone, 'addon_quantity_update');

                    // Update the quantity
                    $addon->quantity = $quantity;
                    $addon->save();

                    $serviceId = $booking?->service_id;
                    $service=Service::where('id', $serviceId)->first();
                    $results = $this->api->getServiceAddons($serviceId);
                    return $this->whatsapp->sendServiceAddonList($phone, $results,$serviceId);
                }
                else if( $list && str_starts_with($list['id'], 'addon_'))
                {
                    $lastAction= $this->getLastAction($phone);
                    // check last action
                    if($lastAction &&  $lastAction->action !== 'include_quantity_update' && $lastAction->action !== 'addon_quantity_update' && $lastAction->action !== 'order_service' && $whatsappOrder)
                    {
                        return $this->whatsapp->sendCancelOrder($phone);
                    }
                    else if($lastAction &&  $lastAction->action !== 'include_quantity_update' && $lastAction->action !== 'addon_quantity_update' && $lastAction->action !== 'order_service' && !$whatsappOrder)
                    {
                        $this->logSession($phone, 'welcome_template');
                        return $this->whatsapp->sendWelcomeTemplate($phone);
                    }
                    $id = str_replace('addon_', '', $list['id']);
                    $title = $list['title'];
                    $this->logSession($phone, 'addon_select_for_order');
                    $client=User::where('phone', $phone)->first();
                    $booking=WhatsAppBooking::where('buyer_id', $client->id)->first();

                    //if not value present then create otherwise update
                    $addon= WhatsAppBookingAddon::where('whats_app_booking_id', $booking->id)
                        ->where('addon_id', $id)
                        ->first();
                    if(!$addon) {
                        // Create a new addon entry
                        $addon = WhatsAppBookingAddon::create([
                            'whats_app_booking_id' => $booking->id,
                            'addon_id' => $id
                        ]);
                    }
                    $addon_details=Serviceadditional::find($id);

                    return $this->whatsapp->sendAddonQuantity($phone, $addon_details);

                }

                else if( $list && str_starts_with($list['id'], 'include_qty_'))
                {
                    $lastAction= $this->getLastAction($phone);
                    // check last action
                    if($lastAction && ($lastAction->action !== 'include_select_for_order' && $lastAction->action !== 'addon_quantity_update' && $lastAction->action !== 'order_service' && $whatsappOrder))
                    {
                        return $this->whatsapp->sendCancelOrder($phone);
                    }
                    else if($lastAction && ($lastAction->action !== 'include_select_for_order' && $lastAction->action !== 'addon_quantity_update' && $lastAction->action !== 'order_service' && !$whatsappOrder))
                    {
                        $this->logSession($phone, 'welcome_template');
                        return $this->whatsapp->sendWelcomeTemplate($phone);
                    }
                    $rawId = str_replace('include_qty_', '', $list['id']);
                    [$includeId, $quantity] = explode('_', $rawId);

                    $id = (int) $includeId;
                    $quantity = (int) $quantity;
                    $client=User::where('phone', $phone)->first();
                    $booking=WhatsAppBooking::where('buyer_id', $client->id)->first();
                    $include=WhatsAppBookingInclude::where('whats_app_booking_id', $booking->id)
                        ->where('include_id', $id)
                        ->first();

                    if(!$include) {
                        return $this->whatsapp->sendText($phone, "Include not found.");
                    }

                    $this->logSession($phone, 'include_quantity_update');

                    // Update the quantity
                    $include->quantity = $quantity;
                    $include->save();

                    $serviceId = $booking?->service_id;
                    $service=Service::where('id', $serviceId)->first();
                    $results = $this->api->getServiceIncludesList($serviceId);
                    return $this->whatsapp->sendServiceIncludeList($phone, $results,$serviceId);
                }
                else if( $list && str_starts_with($list['id'], 'include_'))
                {
                    $lastAction= $this->getLastAction($phone);
                    // check last action
                    if($lastAction &&  $lastAction->action !== 'include_quantity_update' && $lastAction->action !== 'order_service' && $whatsappOrder)
                    {
                        return $this->whatsapp->sendCancelOrder($phone);
                    }
                    else if($lastAction &&  $lastAction->action !== 'include_quantity_update' && $lastAction->action !== 'order_service' && !$whatsappOrder)
                    {
                        $this->logSession($phone, 'welcome_template');
                        return $this->whatsapp->sendWelcomeTemplate($phone);
                    }

                    $id = str_replace('include_', '', $list['id']);
                    $title = $list['title'];
                    $this->logSession($phone, 'include_select_for_order');
                    $client=User::where('phone', $phone)->first();
                    $booking=WhatsAppBooking::where('buyer_id', $client->id)->first();

                    //if not value present then create otherwise update
                    $include= WhatsAppBookingInclude::where('whats_app_booking_id', $booking->id)
                        ->where('include_id', $id)
                        ->first();
                    if(!$include) {
                        // Create a new include entry
                        $include = WhatsAppBookingInclude::create([
                            'whats_app_booking_id' => $booking->id,
                            'include_id' => $id
                        ]);
                    }
                    $include_details=Serviceinclude::find($id);

                    return $this->whatsapp->sendIncludeQuantity($phone, $include_details);

                }
                else if($list && str_starts_with($list['id'], 'continue_include_'))
                {
                    $serviceId = str_replace('continue_include_', '', $list['id']);
                    $results = $this->api->getServiceAddons($serviceId);
                    return $this->whatsapp->sendServiceAddonList($phone, $results,$serviceId);
                }
                else if($list && str_starts_with($list['id'], 'slot_'))
                {

                    $lastAction= $this->getLastAction($phone);
                    if($lastAction && $lastAction->action !== 'set_date_for_order') {
                        $this->logSession($phone, 'welcome_template');
                        return $this->whatsapp->sendWelcomeTemplate($phone);
                    }
                    $slotId = str_replace('slot_', '', $list['id']);
                    //update in database
                    $client=User::where('phone', $phone)->first();
                    $booking=WhatsAppBooking::where('buyer_id', $client->id)->latest('created_at')->first();
                    if($booking) {

                        $bookingIds = WhatsAppBooking::where('buyer_id', $client->id)->where('id', '!=', $booking->id)->pluck('id');

                        if ($bookingIds->isNotEmpty()) {

                            WhatsAppBookingInclude::whereIn('whats_app_booking_id', $bookingIds)->delete();
                            WhatsAppBookingAddon::whereIn('whats_app_booking_id', $bookingIds)->delete();
                            WhatsAppBooking::whereIn('id', $bookingIds)->delete();
                        }

                    }
                    $booking->schedule = $list['title'];
                    $booking->save();
                    $this->logSession($phone, 'select_slot_for_order');
                    $order_service_details= $this->api->getOrderServiceDetails($phone);
                    return $this->whatsapp->sendOrderDetails($phone, $order_service_details);


                }
                else if($list && str_starts_with($list['id'], 'continue_'))
                {
                    $serviceId = str_replace('continue_', '', $list['id']);
                    $service=Service::where('id', $serviceId)->first();
                   if($service->is_service_online === 0)
                    {
                        $value=get_whatsapp_option('whatsapp_message_ask_user_location');
                        $msg=__( $value ?: 'Tell us what address you’d like to take this order.');
                        // If no location found, send location add form
                        return $this->whatsapp->sendAddressRequest($phone,$msg);

                    }
                   $order_service_details= $this->api->getOrderServiceDetails($phone);
                    return $this->whatsapp->sendOrderDetails($phone, $order_service_details);

                }

            }
            // Handle text search after "search_service" button click
            if ($message['type'] === 'text') {
                $text = $message['text']['body'];
                $logs = SessionLog::where('phone', $phone)
                    ->orderBy('created_at', 'desc')
                    ->get();
                $last = null;

                if($logs->isNotEmpty()) {
                    // Get the most recent log
                    $last = $logs[0];
                }
                $isRecent = $last->created_at->diffInMinutes(now()) <= 10;
                // Continue search if user previously clicked "Search Service"
                if ($last && $last->action === 'awaiting_keyword' && $isRecent) {

                    $results = $this->api->searchServices($text);
                    if(isset($results['error']) && $results['error'] === 'No services found') {

                        $value= get_whatsapp_option('whatsapp_message_search_service');
                        $msg = __( $value ?: "No services found for the keyword: {$text}. Please try another keyword.");
                        return $this->whatsapp->sendText($phone, $msg);
                    }
                    $this->logSession($phone, 'searched_service', $text);
                    //forget session
                    $this->sessionManager->forget($phone);
                    $this->sessionManager->store($phone, [
                        'search_results' => $results['data'],
                        'last_action' => 'searched_service'
                    ]);
                    return $this->whatsapp->sendServiceList($phone, $results['data']);
                }
                else
                {
                    $last = null;
                    if($logs->isNotEmpty())
                    {
                        // Get the most recent log
                        $last = $logs[0];
                    }

                    if($last)
                    {
                        $isRecent = $last->created_at->diffInMinutes(now()) <= 10;
                        if($last->action === 'select_location_for_order' && $isRecent)
                        {

                           return $this->sendAvailableSlots($phone, $text);

                        }
                        else if($last->action !== 'talk_to_support')
                        {
                            $this->logSession($phone, 'normal_conversation', $text);
                        }
                    }else{
                        $this->logSession($phone, 'normal_conversation', $text);
                    }


                }

                // First message only
                if ($logs->count() === 1 ) {
                    $this->logSession($phone, 'welcome_template');
                    return $this->whatsapp->sendWelcomeTemplate($phone);

                }
                else if ($logs->count() >= 2) {
                    $last = $logs[0];
                    $secondLast = $logs[1];

                    $isRecent = $last->created_at->diffInMinutes(now()) <= 1;

                    $isGapBig = $last->created_at->diffInMinutes($secondLast->created_at) > 10;

                    $isOutsideWindow = $last->created_at->diffInHours($secondLast->created_at) > 24;
                    if($isRecent && $isOutsideWindow) {
                        return $this->whatsapp->sendOutsideWindowTemplate($phone);
                    }
                    else if ($isRecent && $isGapBig) {
                        $this->logSession($phone, 'welcome_template');
                        return $this->whatsapp->sendWelcomeTemplate($phone);

                    }
                    else if($last->action !== 'talk_to_support'){
                        $this->logSession($phone,'can_help_template');
                        return $this->whatsapp->sendConversationText($phone);
                    }
                }

            }

            if ($message['type'] === 'location') {

                $lastAction = $this->getLastAction($phone);
                // check last action
                if($lastAction && $lastAction->action !== 'addon_quantity_update' && $lastAction->action !== 'include_quantity_update' && $lastAction->action !== 'order_service' )
                {
                    return $this->whatsapp->sendCancelOrder($phone);
                }
                $address = $message['location'];
                if(empty($address['address']))
                {
                    $msg="Please provide a valid address.";
                    return $this->whatsapp->sendAddressRequest($phone,$msg);
                }
                $this->logSession($phone,'select_location_for_order');

                //create it into userlocation then get id
                $client = User::where('phone', $phone)->first();
                $booking=WhatsAppBooking::where('buyer_id', $client->id)->first();
                $booking->address = $address['address'];
                $booking->save();
                $currentDate = Carbon::now();
                $nextDate = $currentDate->copy()->addDay();

                $nextDayOnly = $nextDate->format('j'); // removes the leading 0
                $msg="Location saved successfully.\nPlease provide date within next 30 days for schedule.";
                $msg .= "\n.....................................................\n";
                $msg .= "For example,\ntoday is {$currentDate} if you want to book for tomorrow {$nextDate} send {$nextDayOnly}.";
                return $this->whatsapp->sendText($phone,  $msg);

            }

            if($message['type'] === 'button')
            {
                $button = $message['button']['text'] ?? null;
                 if($button && $button ==='Yes')
                 {
                     $this->logSession($phone,'welcome_template');
                     return $this->whatsapp->sendWelcomeTemplate($phone);
                 }
                 else{
                     $this->logSession($phone,'can_help_template');
                     return $this->whatsapp->sendConversationText($phone);
                 }
            }
        }
        else
        {
            return;
        }
    }

    public function checkLastAction($phone,$lastAction)
    {
        if($lastAction && ($lastAction->action === 'order_service' || $lastAction->action === 'addon_select_for_order' || $lastAction->action === 'addon_quantity_update' || $lastAction->action === 'include_select_for_order'  || $lastAction->action === 'include_quantity_update' || $lastAction->action === 'select_location_for_order' || $lastAction->action === 'set_date_for_order' || $lastAction->action === 'select_slot_for_order' || $lastAction->action === 'order_addon_details' || $lastAction->action === 'order_other_details')) {
            $this->whatsapp->sendCancelOrder($phone);
            return false;
        }
        return true;
    }
    public function getRecentOrderDetails($phone)
    {
        $order_details=$this->api->getRecentOrderDetails($phone);

        if(isset($order_details['error']) && $order_details['error'] === 'User not found') {
            return $this->whatsapp->sendText($phone, "User not found. Please register first.");
        } else if(isset($order_details['error']) && $order_details['error'] === 'No recent orders found') {
            $msg= get_whatsapp_option('whatsapp_message_not_found_recent_order') ?? 'No recent orders found. Please place an order first.';
            return $this->whatsapp->sendText($phone, $msg);
        }
        $status= "Pending";
        if(isset($order_details['status']) && $order_details['status'] === 1 )
        {
            $status="Active";
        }else if(isset($order_details['status']) && $order_details['status'] === 2 ) {
            $status = "Completed";
        }else if(isset($order_details['status']) && $order_details['status'] === 3 ) {
            $status = "Delivered";
        }else if(isset($order_details['status']) && $order_details['status'] === 4 ) {
            $status = "Cancelled";
        }

        $message = "Your recent order details:\n";
        $message .= "Order ID: {$order_details['order_id']}\n";
        $message .= "Service: {$order_details['service_title']}\n";
        $message .= "Price: {$order_details['price']}\n";
        $message .= "Status: {$status}\n";
        $message .= "Payment Gateway: Cash On Delivery\n";
        $message .= "Payment Status: {$order_details['payment_status']}\n";
        $message .= "Scheduled for: {$order_details['booked_at']}\n";

        return $this->whatsapp->sendText($phone, $message);
    }

    public function orderService($phone, $buttonId)
    {
        $user = $this->api->getUserByPhone($phone);

        if (!$user) {
            $user = User::create([
                'name' => $phone,
                'phone' => $phone,
                'username' => $phone,
                'email' => $phone .'@gmail.com',
                'password' => Hash::make(Str::random(16)), //dummy password
                'otp_verified' => 1,
                'email_verified' => 1,
                'user_status' => 1, // Active status
                'user_type' => 1
            ]);

        }

        $serviceId = str_replace('order_service_', '', $buttonId);
        $buyer=User::where('phone', $phone)->first();

        $bookingIds = WhatsAppBooking::where('buyer_id', $buyer->id)->pluck('id');

        if ($bookingIds->isNotEmpty()) {
            WhatsAppBookingInclude::whereIn('whats_app_booking_id', $bookingIds)->delete();
            WhatsAppBookingAddon::whereIn('whats_app_booking_id', $bookingIds)->delete();
            WhatsAppBooking::whereIn('id', $bookingIds)->delete();
        }

        WhatsAppBooking::create([
            'buyer_id' => $buyer->id,
            'service_id' => $serviceId,
        ]);

        $service= Service::where('id', $serviceId)->first();
        if($service->is_service_online === 1)
        {
            $results = $this->api->getServiceAddons($serviceId);
            return $this->whatsapp->sendServiceAddonList($phone, $results,$serviceId);
        }

        $results = $this->api->getServiceIncludesList($serviceId);
        return $this->whatsapp->sendServiceIncludeFirstList($phone, $results,$serviceId);
    }

    public function cancelOrderNo($phone)
    {
        $buyer= User::where('phone', $phone)->first();
        $booking= WhatsAppBooking::where('buyer_id', $buyer->id)->first();
        $service= Service::where('id', $booking?->service_id)->first();
        $msg = __("Your order wasn't cancelled. Feel free to continue ordering.");

        $lastAction= $this->getLastAction($phone);
        if($lastAction &&$lastAction->action==='order_service' && $service?->is_service_online === 1)
        {
            $msg=__("Please select addon or skip this.");
        }
        else if ($lastAction &&$lastAction->action==='order_service' && $service?->is_service_online === 0)
        {
            $msg=__("Please select include");
        }
        else if($lastAction && $lastAction->action === 'addon_select_for_order')
        {
            $msg=__("Please select addon quantity.");
        }
        else if($lastAction && $lastAction->action === 'addon_quantity_update')
        {
            $msg=__("Please select another addon or give location for this order.");
        }
        else if($lastAction && $lastAction->action === 'include_select_for_order')
        {
            $msg=__("Please select include quantity.");
        }
        else if($lastAction && $lastAction->action === 'include_quantity_update')
        {
            $msg=__("Please select another include or select addon for this order.");
        }

        else if($lastAction && $lastAction->action === 'select_location_for_order')
        {
            $msg=__("Please provide date for this order.");
        }
        else if($lastAction && $lastAction->action === 'set_date_for_order')
        {
            $msg=__("Please select slot for this order.");
        }
        return $this->whatsapp->sendText($phone, $msg);
    }

    public function confirmOrderYes($phone)
    {
        $client=User::where('phone', $phone)->first();
        $booking=WhatsAppBooking::where('buyer_id', $client->id)->first();

        $order_details=$this->api->placeOrder($booking?->service_id, $phone);
        if(isset($order_details['error']) && $order_details['error'] === 'You have to give your address') {
            return $this->whatsapp->sendText($phone, "You have to give your address");
        } else if(isset($order_details['error']) && $order_details['error'] === 'Kindly provide a date on which you would like to receive the service.') {
            return $this->whatsapp->sendText($phone, "Kindly provide a date on which you would like to receive the service.");
        }else if(isset($order_details['error']) && $order_details['error'] === 'You have to give your schedule') {
            return $this->whatsapp->sendText($phone, "You have to give your schedule");
        }else if(isset($order_details['error']) && $order_details['error'] === 'Service price is 0, order cannot be created. Please try other services.') {
            return $this->whatsapp->sendText($phone, "Service price is 0, order cannot be created. Please try other services.");
        }

        $this->logSession($phone, 'confirm_order');

        $bookingIds = WhatsAppBooking::where('buyer_id', $client->id)->pluck('id');
        if ($bookingIds->isNotEmpty()) {
            WhatsAppBookingInclude::whereIn('whats_app_booking_id', $bookingIds)->delete();
            WhatsAppBookingAddon::whereIn('whats_app_booking_id', $bookingIds)->delete();
            WhatsAppBooking::whereIn('id', $bookingIds)->delete();
        }

        //Clear sessionManager stored data for the user
        $this->sessionManager->forget($phone);

        $status="Pending";
        if($order_details['status'] === 1) {
            $status = "Completed";
        }

        $value = get_whatsapp_option('whatsapp_message_order_complete');
        if($order_details['service_type'] === 1)
        {
            $service_type= 'Online';
        }else
        {
            $service_type= 'Offline';
        }

        $msg= __( $value ?: 'Your order has been placed successfully. We will contact you soon.');

        $message = "{$msg}\n";
        $message .= "___________________________\n";
        $message .= "Order ID: {$order_details['order_id']}\n";
        $message .= "Service: {$order_details['service_title']}\n";
        $message .= "Service Type: {$service_type}\n";
        $message .= "Price: {$order_details['price']}\n";
        $message .= "Status: {$status}\n";
        $message .= "Payment Gateway: Cash On Delivery\n";
        $message .= "Payment Status: {$order_details['payment_status']}\n";
        return $this->whatsapp->sendText($phone, $message);
    }

    public function sendAvailableSlots($phone,$text)
    {
        $client = User::where('phone', $phone)->first();
        $booking = WhatsAppBooking::where('buyer_id', $client->id)->first();


        if (is_numeric($text) && (int)$text >= 1 && (int)$text <= 31) {

            $today= Carbon::now();
            $currentDay= $today->day;
            $currentMonth = $today->month;
            $currentYear = $today->year;
            if ($text < $currentDay) {
                $nextMonthDate = $today->copy()->addMonthNoOverflow();
                $targetMonth = $nextMonthDate->month;
                $targetYear = $nextMonthDate->year;
            } else {
                $targetMonth = $currentMonth;
                $targetYear = $currentYear;
            }

            $fullDate = Carbon::create($targetYear, $targetMonth, $text)->format('Y-m-d');

            //update date in booking table
            $booking->date = $fullDate;
            $booking->save();
            $available_slots=$this->api->getAvailableSlots($phone, $booking?->service_id, $fullDate);
            if(!$available_slots)
            {
                $msg = get_whatsapp_option('whatsapp_message_not_available_slots') ?? "No available slots found for {$fullDate}. Please try another date.";
                if(get_whatsapp_option('whatsapp_message_not_available_slots') && strpos(get_whatsapp_option('whatsapp_message_not_available_slots'), '{date}') !== false) {
                    $msg = str_replace('{date}', $fullDate, get_whatsapp_option('whatsapp_message_not_available_slots'));
                }

                $msg=__($msg);

                return $this->whatsapp->sendText($phone, $msg);
            }
            $this->logSession($phone, 'set_date_for_order');
            return $this->whatsapp->sendAvailableSlots($phone, $available_slots);
        }else
        {
            $currentDate = Carbon::now();
            $nextDate = $currentDate->copy()->addDay();

            $nextDayOnly = $nextDate->format('j');
            $msg="Please provide date within next 30 days for schedule.\n";
            $msg .= "...........................................\n";
            $msg .= "For example,\ntoday is {$currentDate} if you want to book for tomorrow {$nextDate} send {$nextDayOnly}.";

            $msg = get_whatsapp_option('whatsapp_message_ask_provide_date') ?: $msg;
            if(get_whatsapp_option('whatsapp_message_ask_provide_date') && strpos(get_whatsapp_option('whatsapp_message_ask_provide_date'), '{currentDate}') !== false) {
                $msg = str_replace('{currentDate}', $currentDate, get_whatsapp_option('whatsapp_message_ask_provide_date'));
            }
            if(get_whatsapp_option('whatsapp_message_ask_provide_date') && strpos(get_whatsapp_option('whatsapp_message_ask_provide_date'), '{nextDate}') !== false) {
                $msg = str_replace('{nextDate}', $nextDate, get_whatsapp_option('whatsapp_message_ask_provide_date'));
            }
            if(get_whatsapp_option('whatsapp_message_ask_provide_date') && strpos(get_whatsapp_option('whatsapp_message_ask_provide_date'), '{nextDayOnly}') !== false) {
                $msg = str_replace('{nextDayOnly}', $nextDayOnly, get_whatsapp_option('whatsapp_message_ask_provide_date'));
            }

            $msg=__($msg);
            return $this->whatsapp->sendText($phone, $msg);
        }

    }


    public function getLastAction($phone)
    {
        $last = SessionLog::where('phone', $phone)->latest('id')->first();
        return $last;
    }

    public function logSession($phone, $action, $keyword = null)
    {
        SessionLog::create([
            'phone' => $phone,
            'action' => $action,
            'keyword' => $keyword
        ]);

        $logs = SessionLog::where('phone', $phone)
            ->orderBy('created_at', 'desc')
            ->skip(10)
            ->take(10)
            ->get();

        foreach ($logs as $log) {
            $log->delete();
        }

    }

    public function deleteExitingInfo($phone)
    {
        $client = User::where('phone', $phone)->first();
        $bookingIds = WhatsAppBooking::where('buyer_id', $client->id)->pluck('id');
        if ($bookingIds->isNotEmpty()) {
            WhatsAppBookingInclude::whereIn('whats_app_booking_id', $bookingIds)->delete();
            WhatsAppBookingAddon::whereIn('whats_app_booking_id', $bookingIds)->delete();
            WhatsAppBooking::whereIn('id', $bookingIds)->delete();
        }
    }
}
