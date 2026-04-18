<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $table = 'pages';
    protected $fillable = ['title','title_en','slug','page_content','page_content_en','status','visibility','page_builder_status','layout','sidebar_layout','navbar_variant',
        'page_class','back_to_top','breadcrumb_status','footer_variant','widget_style','left_column','right_column'];

    public function meta_data(){
        return $this->morphOne(MetaData::class,'meta_taggable');
    }

    /**
     * Cache of English translations keyed by page ID.
     */
    private static $enTranslationCache = [];

    /**
     * Return the English title when the active language is NOT Italian.
     */
    public function getTitleAttribute($value)
    {
        if ($this->shouldShowEnglish()) {
            $en = $this->getEnTranslation('title_en');
            if (!empty($en)) {
                return $en;
            }
        }
        return $value;
    }

    /**
     * Return the English page content when the active language is NOT Italian.
     */
    public function getPageContentAttribute($value)
    {
        if ($this->shouldShowEnglish()) {
            $en = $this->getEnTranslation('page_content_en');
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
            self::$enTranslationCache[$id] = \Illuminate\Support\Facades\DB::table('pages')
                ->where('id', $id)
                ->select('title_en', 'page_content_en')
                ->first();
        }

        $cached = self::$enTranslationCache[$id];
        return $cached->{$field} ?? null;
    }
}
