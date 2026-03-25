<?php

namespace Modules\WhatsAppBookingSystem\Handlers;

use Illuminate\Support\Facades\Cache;

class SessionManager
{
    public function store($phone, $data)
    {
        Cache::put("phone_$phone", $data);
    }

    public function get($phone)
    {
        return Cache::get("phone_$phone");
    }

    public function forget($phone)
    {
        Cache::forget("phone_$phone");
    }
}
