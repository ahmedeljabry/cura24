<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';
    protected $fillable = [
        'category_id',
        'subcategory_id',
        'child_category_id',
        'seller_id',
        'service_city_id',
        'service_area_id',
        'title',
        'title_en',
        'slug',
        'description',
        'description_en',
        'image',
        'status',
        'is_service_on',
        'price',
        'tax',
        'view',
        'featured',
        'image_gallery',
        'video',
        'is_service_all_cities',
        'is_service_online',
        'delivery_days',
        'revision',
    ];

    
    public function category(){
        return $this->belongsTo('App\Category');
    }

    public function subcategory(){
        return $this->belongsTo('App\Subcategory');
    }

    public function childcategory(){
        return $this->belongsTo(ChildCategory::class, 'child_category_id', 'id');
    }

    public function serviceInclude(){
        return $this->hasMany('App\Serviceinclude');
    }

    public function serviceAdditional(){
        return $this->hasMany('App\Serviceadditional');
    }

    public function serviceBenifit(){
        return $this->hasMany('App\Servicebenifit');
    }

    public function serviceFaq(){
        return $this->hasMany('App\OnlineServiceFaq');
    }

    public function seller(){
        return $this->belongsTo('App\User','seller_id','id');
    }

    public function seller_for_mobile(){
        return $this->belongsTo('App\User','seller_id','id')->select('id','name','image','country_id', 'phone', 'service_city', 'service_area', 'address', 'latitude', 'longitude','seller_address', 'post_code', 'username');
    }

    public function reviews(){
        return $this->hasMany(Review::class,'service_id','id');
    }

    public function reviews_for_mobile(){
        return $this->hasMany(Review::class,'service_id','id')
            ->select('id','service_id','rating','message','buyer_id');
    }

    public function pendingOrder(){
        return $this->hasMany(Order::class,'service_id','id')->where('status',0);
    }

    public function completeOrder(){
        return $this->hasMany(Order::class,'service_id','id')->where('status',2);
    }

    public function cancelOrder(){
        return $this->hasMany(Order::class,'service_id','id')->where('status',4);
    }

    public function avgFeedback() {

        return $this->hasMany(Review::class, 'service_id', 'id')
                        ->selectRaw('service_id,AVG(reviews.rating) AS average_rating');
    }

    public function metaData(){
        return $this->morphOne(MetaData::class,'meta_taggable');
    }

    public function serviceCity(){
        return $this->belongsTo(ServiceCity::class,'service_city_id','id');
    }

    public function seller_subscription(){
        $number_of_connect = get_static_option('set_number_of_connect',2);
        return $this->belongsTo('\Modules\Subscription\Entities\SellerSubscription','seller_id','seller_id')
            ->where('connect','>=',$number_of_connect)
            ->where('expire_date','>=',date('Y-m-d'));
    }

    public function favouritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favourite_services', 'service_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'integer',
    ];



    // for google map
    public function scopeSearchResults($query)
    {
        return $query->where('status', 1)
            ->when(request()->filled('search'), function($query) {
                $query->where(function($query) {
                    $search = request()->input('search');
                    $query->where('title', 'LIKE', "%$search%")
                        ->orWhere('description', 'LIKE', "%$search%")
                        ->orWhere('address', 'LIKE', "%$search%");
                });
            })
            ->when(request()->filled('category'), function($query) {
                $query->whereHas('categories', function($query) {
                    $query->where('id', request()->input('category'));
                });
            });
    }

    public function whatsAppBookings()
    {
       if(moduleExists('WhatsAppBookingSystem')) {
            return $this->hasMany('Modules\WhatsAppBookingSystem\Entities\WhatsAppBooking', 'service_id', 'id');
        }
        return null;
    }

    /**
     * Cache of English translations keyed by service ID.
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
        // If the column was already loaded by the query, use it directly.
        if (array_key_exists($field, $this->attributes)) {
            return $this->attributes[$field];
        }

        // Lazy-load from the database (with a static cache so each row is
        // fetched at most once per request).
        $id = $this->attributes['id'] ?? null;
        if ($id === null) {
            return null;
        }

        if (!isset(self::$enTranslationCache[$id])) {
            self::$enTranslationCache[$id] = \Illuminate\Support\Facades\DB::table('services')
                ->where('id', $id)
                ->select('title_en', 'description_en')
                ->first();
        }

        $cached = self::$enTranslationCache[$id];
        return $cached->{$field} ?? null;
    }
}

