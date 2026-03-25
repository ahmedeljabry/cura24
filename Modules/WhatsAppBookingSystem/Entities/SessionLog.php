<?php

namespace Modules\WhatsAppBookingSystem\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SessionLog extends Model
{
    use HasFactory;

   protected $fillable= [
        'phone',
        'action',
        'keyword',
    ];
    
    protected static function newFactory()
    {
        return \Modules\WhatsAppBookingSystem\Database\factories\SessionLogFactory::new();
    }
}
