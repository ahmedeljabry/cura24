<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blogs';
    protected $fillable = ['category_id',
        'user_id','title','slug','blog_content',
        'image','author','excerpt','status','views',
        'visibility','featured','schedule_date',
        'admin_id','created_by','tag_name',
        'title_en', 'blog_content_en', 'excerpt_en', 'tag_name_en'
    ];

    private function shouldShowEnglish(): bool
    {
        if (request()->is('admin-home*') || request()->is('seller*') || request()->is('api/v1/seller*')) {
            return false;
        }

        $lang = \App\Helpers\LanguageHelper::user_lang_slug();
        return !in_array($lang, ['it', 'it_IT']);
    }

    public function getTitleAttribute($value) {
        if ($this->shouldShowEnglish() && !empty($this->attributes['title_en'])) {
            return $this->attributes['title_en'];
        }
        return $value;
    }

    public function getBlogContentAttribute($value) {
        if ($this->shouldShowEnglish() && !empty($this->attributes['blog_content_en'])) {
            return $this->attributes['blog_content_en'];
        }
        return $value;
    }

    public function getExcerptAttribute($value) {
        if ($this->shouldShowEnglish() && !empty($this->attributes['excerpt_en'])) {
            return $this->attributes['excerpt_en'];
        }
        return $value;
    }

    public function getTagNameAttribute($value) {
        if ($this->shouldShowEnglish() && !empty($this->attributes['tag_name_en'])) {
            return $this->attributes['tag_name_en'];
        }
        return $value;
    }

    protected $dates = ['deleted_at'];


    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function admin(){
        return $this->belongsTo(Admin::class,'admin_id');
    }

    public function meta_data(){
        return $this->morphOne(MetaData::class,'meta_taggable');
    }

    public function author_data(){
        if ($this->attributes['created_by'] === 'user'){
            return User::find($this->attributes['user_id']);
        }
        return Admin::find($this->attributes['admin_id']);
    }

    public function category(){
        return $this->belongsTo(Category::class,'category_id','id');  
    }

    public function comments(){
        return $this->hasMany(BlogComment::class,'blog_id','id');  
    }

}
