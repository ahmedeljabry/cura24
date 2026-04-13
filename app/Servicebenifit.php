<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicebenifit extends Model
{
    use HasFactory;

    protected $table = 'servicebenifits';
    protected $fillable = ['seller_id','service_id','benifits'];

    public function getBenifitsAttribute($value)
    {
        if ($this->shouldShowEnglish()) {
            if (!empty($this->attributes['benifits_en'])) {
                return $this->attributes['benifits_en'];
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
