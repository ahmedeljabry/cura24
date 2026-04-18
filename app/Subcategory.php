<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;
    protected $table = 'subcategories';
    protected $fillable = ['name','name_en','slug','category_id','status','image', 'description','description_en'];

    public function category(){
        return $this->belongsTo('App\Category');
    }

    public function childcategories(){
        return $this->hasMany('App\ChildCategory');
    }

    public function services(){
        return $this->hasMany('App\Service');
    }
    public function metaData(){
        return $this->morphOne(MetaData::class,'meta_taggable');
    }

    /**
     * Cache of English translations keyed by subcategory ID.
     */
    private static $enTranslationCache = [];

    /**
     * Return the English name when the active language is NOT Italian.
     */
    public function getNameAttribute($value)
    {
        if ($this->shouldShowEnglish()) {
            $en = $this->getEnTranslation('name_en');
            if (!empty($en)) {
                return $en;
            }
        }
        return $value;
    }

    /**
     * Return the English description when the active language is NOT Italian.
     */
    public function getDescriptionAttribute($value)
    {
        if ($this->shouldShowEnglish()) {
            $en = $this->getEnTranslation('description_en');
            if (!empty($en)) {
                return $en;
            }
        }
        return $value;
    }

    /**
     * Check whether the current session language is NOT Italian.
     */
    private function shouldShowEnglish(): bool
    {
        if (request()->is('admin-home*') || request()->is('seller*') || request()->is('api/v1/seller*')) {
            return false;
        }

        $lang = \App\Helpers\LanguageHelper::user_lang_slug();
        return !in_array($lang, ['it', 'it_IT']);
    }

    /**
     * Get an English translation field, lazy-loading from DB when the column
     * was not included in the original SELECT.
     */
    private function getEnTranslation(string $field): ?string
    {
        if (array_key_exists($field, $this->attributes)) {
            return $this->attributes[$field];
        }

        $id = $this->attributes['id'] ?? null;
        if ($id === null) {
            return null;
        }

        if (!isset(self::$enTranslationCache[$id])) {
            self::$enTranslationCache[$id] = \Illuminate\Support\Facades\DB::table('subcategories')
                ->where('id', $id)
                ->select('name_en', 'description_en')
                ->first();
        }

        $cached = self::$enTranslationCache[$id];
        return $cached->{$field} ?? null;
    }
}
