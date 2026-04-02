<?php

namespace App\Services;

use Exception;

class WhatsAppNotifier
{
    /**
     * Send a WhatsApp text message to a phone number.
     * Silently fails if the module is missing or unconfigured.
     *
     * @param string|null $phone  E.164 format (e.g. +966501234567)
     * @param string      $message
     * @return void
     */
    public static function notify(?string $phone, string $message): void
    {
        if (empty($phone) || !moduleExists('WhatsAppBookingSystem')) {
            return;
        }

        $phoneNumberId = get_whatsapp_option('whatsapp_phone_number_id');
        $accessToken   = get_whatsapp_option('whatsapp_permanent_token');

        if (empty($phoneNumberId) || empty($accessToken)) {
            return; // Not configured — skip silently
        }

        try {
            $service = new \Modules\WhatsAppBookingSystem\Http\Services\WhatsAppService();
            $service->sendText($phone, $message);
        } catch (Exception $e) {
            // Log error but don't interrupt order processing
            \Illuminate\Support\Facades\Log::warning('WhatsApp notification failed: ' . $e->getMessage(), [
                'phone' => $phone,
            ]);
        }
    }
}
