<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Serviceinclude extends Model
{
    use HasFactory;

    protected $table = 'serviceincludes';
    protected $fillable = ['seller_id', 'service_id', 'include_service_title', 'include_service_price', 'include_service_quantity'];

    public function getIncludeServiceTitleAttribute($value)
    {
        if ($this->shouldShowEnglish()) {
            if (!empty($this->attributes['include_service_title_en'])) {
                return $this->attributes['include_service_title_en'];
            }
        }
        return $value;
    }

    private function shouldShowEnglish(): bool
    {
        $lang = session()->get('lang');
        if (empty($lang)) {
            return true;
        }
        return $lang !== 'it_IT';
    }
}
