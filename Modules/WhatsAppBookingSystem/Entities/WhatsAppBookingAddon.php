<?php

namespace Modules\WhatsAppBookingSystem\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WhatsAppBookingAddon extends Model
{
    use HasFactory;

    protected $fillable = [
        'whats_app_booking_id',
        'addon_id',
        'quantity',
    ];
    
    protected static function newFactory()
    {
        return \Modules\WhatsAppBookingSystem\Database\factories\WhatsAppBookingAddonFactory::new();
    }

    public function whatsAppBooking()
    {
        return $this->belongsTo('Modules\WhatsAppBookingSystem\Entities\WhatsAppBooking', 'whats_app_booking_id', 'id');
    }
}
