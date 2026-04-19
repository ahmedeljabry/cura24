<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogUpdateRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'category_id' => 'required',
            'tag_id' => 'nullable',
            'blog_content' => 'required',
            'blog_content_en' => 'nullable',
            'tags' => 'nullable',
            'excerpt' => 'nullable',
            'excerpt_en' => 'nullable',
            'title' => 'required|string|max:191',
            'title_en' => 'nullable|string|max:191',
            'status' => 'nullable',
            'author' => 'nullable',
            'slug' => 'nullable',
            'meta_tags' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'image' => 'nullable|string|max:191',
        ];
    }
}
