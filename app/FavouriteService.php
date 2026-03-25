<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavouriteService extends Model
{
    protected $fillable = [
        'user_id',
        'service_id',
    ];
}
