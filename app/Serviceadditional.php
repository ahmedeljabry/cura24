<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Serviceadditional extends Model
{
    use HasFactory;
    protected $table = 'serviceadditionals';
    protected $fillable = ['seller_id','service_id','additional_service_title','additional_service_price','additional_service_quantity','additional_service_image'];

    public function getAdditionalServiceTitleAttribute($value)
    {
        if ($this->shouldShowEnglish()) {
            if (!empty($this->attributes['additional_service_title_en'])) {
                return $this->attributes['additional_service_title_en'];
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
