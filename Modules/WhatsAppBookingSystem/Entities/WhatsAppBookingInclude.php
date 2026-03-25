<?php

namespace Modules\WhatsAppBookingSystem\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WhatsAppBookingInclude extends Model
{
    use HasFactory;

    protected $fillable = [
        'whats_app_booking_id',
        'include_id',
        'quantity',
    ];
    
    protected static function newFactory()
    {
        return \Modules\WhatsAppBookingSystem\Database\factories\WhatsAppBookingIncludeFactory::new();
    }
}
