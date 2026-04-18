<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;
    
    protected $table = 'categories';
    protected $fillable = ['name', 'name_en', 'slug','icon','image','status','mobile_icon', 'description', 'description_en'];

    public function subcategories(){
        return $this->hasMany(Subcategory::class,'category_id','id');
    }

    public function services(){
        return $this->hasMany(Service::class,'category_id','id')->where('status',1)->where('is_service_on',1);
    }

    public function metaData(){
        return $this->morphOne(MetaData::class,'meta_taggable');
    }

    /**
     * Cache of English translations keyed by category ID.
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
        $lang = session()->get('lang');

        if (empty($lang)) {
            return true;
        }

        return $lang !== 'it_IT';
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
            self::$enTranslationCache[$id] = \Illuminate\Support\Facades\DB::table('categories')
                ->where('id', $id)
                ->select('name_en', 'description_en')
                ->first();
        }

        $cached = self::$enTranslationCache[$id];
        return $cached->{$field} ?? null;
    }
}
