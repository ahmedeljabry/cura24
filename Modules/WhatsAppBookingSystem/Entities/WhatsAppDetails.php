<?php

namespace Modules\WhatsAppBookingSystem\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WhatsAppDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'option_name',
        'option_value',
    ];
    
    protected static function newFactory()
    {
        return \Modules\WhatsAppBookingSystem\Database\factories\WhatsAppDetailsFactory::new();
    }
}
