<?php

namespace Modules\WhatsAppBookingSystem\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WhatsAppBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'service_id',
        'address',
        'date',
        'schedule',
    ];
    
    protected static function newFactory()
    {
        return \Modules\WhatsAppBookingSystem\Database\factories\WhatsAppBookingFactory::new();
    }

    public function service()
    {
        return $this->belongsTo('App\Service', 'service_id', 'id');
    }
    public function buyer()
    {
        return $this->belongsTo('App\User', 'buyer_id', 'id');
    }
}
