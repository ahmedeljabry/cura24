<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\WhatsAppBookingSystem\Http\Controllers\Api\WebhookController;
use Modules\WhatsAppBookingSystem\Http\Controllers\Api\WhatsAppFunctionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/whatsappbookingsystem', function (Request $request) {
    return $request->user();
});

Route::group(['prefix'=>'v1'],function(){

    Route::match(['GET', 'POST'], '/whatsapp/webhook', [WebhookController::class, 'receive']);


    // Route::post('whatsapp/webhook', [WebhookController::class, 'receive']);
    //whatsapp booking
    Route::get('whatsapp/search-service/{keyword}', [WhatsAppFunctionController::class, 'searchService'])->name('whatsapp.services.search');
    Route::get('whatsapp/service-addons/{id}', [WhatsAppFunctionController::class, 'getServiceAddons'])->name('whatsapp.services.addons');
    Route::get('whatsapp/service-includes/{id}', [WhatsAppFunctionController::class, 'getServiceIncludes'])->name('whatsapp.services.includes');
    Route::get('whatsapp/service-details/{id}', [WhatsAppFunctionController::class, 'serviceDetails'])->name('whatsapp.services.details');
    Route::get('whatsapp/service-includes-addons/{id}', [WhatsAppFunctionController::class, 'serviceInclude'])->name('whatsapp.services.includes.addons');
    Route::get('whatsapp/service-faqs/{id}', [WhatsAppFunctionController::class, 'serviceFaq'])->name('whatsapp.services.faqs');
    Route::get('whatsapp/user/recent-order-details/{phone}', [WhatsAppFunctionController::class, 'recentOrderDetails'])->name('whatsapp.user.recent-order.details');
    Route::get('whatsapp/user/by-phone/{phone}', [WhatsAppFunctionController::class, 'getUserByPhone'])->name('whatsapp.user.by-phone');
    Route::get('whatsapp/service/staff-lists/{service_id}', [WhatsAppFunctionController::class, 'getStaffList'])->name('whatsapp.service.staff-lists');
    Route::get('whatsapp/client/location-lists/{client_id}', [WhatsAppFunctionController::class, 'getLocationList'])->name('whatsapp.client.location-lists');
    Route::get('whatsapp/available-slots/{phone}/{serviceId}/{date}', [WhatsAppFunctionController::class, 'getAvaliableSlots'])->name('whatsapp.service.available-slots');
    Route::get('whatsapp/order-service-details/{phone}', [WhatsAppFunctionController::class, 'orderServiceDetails'])->name('whatsapp.order.service.details');
    Route::get('whatsapp/order-addons/{phone}', [WhatsAppFunctionController::class, 'orderAddonDetails'])->name('whatsapp.order.addons.details');
    Route::get('whatsapp/order-other-details/{phone}', [WhatsAppFunctionController::class, 'orderOtherDetails'])->name('whatsapp.order.other.details');
    Route::post('whatsapp/order/create', [WhatsAppFunctionController::class, 'placeOrder'])->name('whatsapp.order.create');


});