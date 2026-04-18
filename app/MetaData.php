<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaData extends Model
{
    use HasFactory;
    protected $table = 'meta_data';
    protected $fillable = [
        'meta_taggable_id','meta_taggable_type',
        'meta_title','meta_title_en',
        'meta_tags','meta_tags_en',
        'meta_description','meta_description_en',
        'facebook_meta_tags','facebook_meta_tags_en',
        'facebook_meta_description','facebook_meta_description_en',
        'facebook_meta_image',
        'twitter_meta_tags','twitter_meta_tags_en',
        'twitter_meta_description','twitter_meta_description_en',
        'twitter_meta_image'
    ];

    public function meta_taggable(){
        return $this->morphTo();
    }

    private function shouldShowEnglish(): bool
    {
        $lang = session()->get('lang');
        if (empty($lang)) return true;
        return $lang !== 'it_IT';
    }

    public function getMetaTitleAttribute($value) {
        return ($this->shouldShowEnglish() && !empty($this->attributes['meta_title_en'])) ? $this->attributes['meta_title_en'] : $value;
    }

    public function getMetaTagsAttribute($value) {
        return ($this->shouldShowEnglish() && !empty($this->attributes['meta_tags_en'])) ? $this->attributes['meta_tags_en'] : $value;
    }

    public function getMetaDescriptionAttribute($value) {
        return ($this->shouldShowEnglish() && !empty($this->attributes['meta_description_en'])) ? $this->attributes['meta_description_en'] : $value;
    }

    public function getFacebookMetaTagsAttribute($value) {
        return ($this->shouldShowEnglish() && !empty($this->attributes['facebook_meta_tags_en'])) ? $this->attributes['facebook_meta_tags_en'] : $value;
    }

    public function getFacebookMetaDescriptionAttribute($value) {
        return ($this->shouldShowEnglish() && !empty($this->attributes['facebook_meta_description_en'])) ? $this->attributes['facebook_meta_description_en'] : $value;
    }

    public function getTwitterMetaTagsAttribute($value) {
        return ($this->shouldShowEnglish() && !empty($this->attributes['twitter_meta_tags_en'])) ? $this->attributes['twitter_meta_tags_en'] : $value;
    }

    public function getTwitterMetaDescriptionAttribute($value) {
        return ($this->shouldShowEnglish() && !empty($this->attributes['twitter_meta_description_en'])) ? $this->attributes['twitter_meta_description_en'] : $value;
    }
}
