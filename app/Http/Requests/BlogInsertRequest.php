<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogInsertRequest extends FormRequest
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
            'title' => 'required|string|max:191',
            'blog_content' => 'required',
            'title_en' => 'nullable|string|max:191',
            'blog_content_en' => 'nullable',
            'excerpt_en' => 'nullable',
            'status' => 'nullable',
            'slug' => 'nullable',
            'image' => 'nullable|string|max:191',
        ];
    }
}
