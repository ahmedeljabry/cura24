<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Modules\WhatsAppBookingSystem\Http\Controllers\WhatsAppBookingSystemController;

Route::group(['as'=>'admin.','prefix'=>'admin-home/whatsapp','middleware' => ['auth:admin','setlang']],function() {
    //whatsapp booking system
    Route::get('settings', [WhatsAppBookingSystemController::class, 'whatsappSettingPage'])->name('whatsapp.setting')->permission('whatsapp-setting');
    Route::post('settings/update', [WhatsAppBookingSystemController::class, 'whatsappSettingUpdate'])->name('whatsapp.setting.update')->permission('whatsapp-setting-update');
    Route::post('message/setting/update', [WhatsAppBookingSystemController::class, 'messageSettingUpdate'])->name('whatsapp.message.setting.update')->permission('whatsapp-message-setting-update');
    Route::get('message/setting', [WhatsAppBookingSystemController::class, 'messageSettingPage'])->name('whatsapp.message.setting')->permission('whatsapp-message-setting');
    Route::post('button-text/setting/update', [WhatsAppBookingSystemController::class, 'buttonTextSettingUpdate'])->name('whatsapp.button-text.setting.update')->permission('whatsapp-button-text-setting-update');
    Route::get('button-text/setting', [WhatsAppBookingSystemController::class, 'buttonTextSettingPage'])->name('whatsapp.button-text.setting')->permission('whatsapp-button-text-setting');
    Route::get('message/template-guide', [WhatsAppBookingSystemController::class, 'messageTemplateGuide'])->name('whatsapp.message.template.guide')->permission('whatsapp-message-template-guide');

    Route::get('otp-settings', [WhatsAppBookingSystemController::class, 'whatsappOtpSettingPage'])->name('whatsapp.otp.setting')->permission('whatsapp-setting');
    Route::post('otp-settings/update', [WhatsAppBookingSystemController::class, 'whatsappOtpSettingUpdate'])->name('whatsapp.otp.setting.update')->permission('whatsapp-setting');

});
