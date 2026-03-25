<?php

namespace Modules\WhatsAppBookingSystem\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Modules\WhatsAppBookingSystem\Handlers\MessageHandler;

class WebhookController extends Controller
{


    public function receive(Request $request)
    {
        if ($request->isMethod('get')) {
            $verifyToken = get_whatsapp_option('whatsapp_verify_token') ?? Config::get('services.whats_app.verify_token');
            $mode = $request->input('hub_mode');
            $token = $request->input('hub_verify_token');
            $challenge = $request->input('hub_challenge');

            if ($mode === 'subscribe' && $token === $verifyToken) {
                return response($challenge, 200)->header('Content-Type', 'text/plain');
            }

            return response('Forbidden', 403);
        }

        if ($request->isMethod('post')) {
            $phone = $request->input('entry.0.changes.0.value.messages.0.from');
            $message =$request->input('entry.0.changes.0.value.messages.0');
            $handler = new MessageHandler();
            $response=$handler->handle($phone, $message);
            return $response;
        }
    }

}
