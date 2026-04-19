@extends('backend.admin-master')

@section('site-title')
    {{__('Edit Category')}}
@endsection
@section('style')
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/jodit.fat.min.css')}}">
    <x-summernote.css/>
    <x-media.css/>
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40"></div>
                <x-msg.success/>
                <x-msg.error/>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="header-wrap d-flex justify-content-between">
                            <div class="left-content">
                                <h4 class="header-title">{{__('Edit Category')}}   </h4>
                            </div>
                            <div class="right-content">
                                <a class="btn btn-info btn-sm" href="{{route('admin.category')}}">{{__('All Categories')}}</a>
                            </div>
                        </div>
                        <form action="{{route('admin.category.edit',$category->id)}}" method="post" enctype="multipart/form-data" id="edit_category_form">
                            @csrf

                            <div class="tab-content margin-top-40">
                                <div class="card mb-4">
                                    <div class="card-header bg-transparent border-bottom-0">
                                        <ul class="nav nav-tabs" id="categoryLangTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link active" id="category-it-tab" data-toggle="tab" href="#category-it" role="tab" aria-controls="category-it" aria-selected="true" style="color: blue">{{__('Italian (Default)')}}</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" id="category-en-tab" data-toggle="tab" href="#category-en" role="tab" aria-controls="category-en" aria-selected="false" style="color: blue">{{__('English')}}</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content" id="categoryLangTabContent">
                                            <!-- Italian Tab -->
                                            <div class="tab-pane fade show active" id="category-it" role="tabpanel" aria-labelledby="category-it-tab">
                                                <div class="form-group">
                                                    <label for="name">{{__('Name (Italian)')}}</label>
                                                    <input type="text" class="form-control" name="name" id="name" value="{{$category->name}}" placeholder="{{__('Name')}}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{__('Description (Italian)')}}</label>
                                                    <textarea id="jodit-editor" style="height: 400px;"></textarea>
                                                    <textarea name="description" id="description" class="d-none">{{$category->description}}</textarea>
                                                </div>
                                            </div>
                                            <!-- English Tab -->
                                            <div class="tab-pane fade" id="category-en" role="tabpanel" aria-labelledby="category-en-tab">
                                                <div class="form-group">
                                                    <label for="name_en">{{__('Name (English)')}}</label>
                                                    <input type="text" class="form-control" name="name_en" id="name_en" value="{{$category->name_en}}" placeholder="{{__('Name (English)')}}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{__('Description (English)')}}</label>
                                                    <textarea id="jodit-editor-en" style="height: 400px;"></textarea>
                                                    <textarea name="description_en" id="description_en" class="d-none">{{$category->description_en}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group permalink_label">
                                    <label class="text-dark">{{__('Permalink * :')}}
                                        <span id="slug_show" class="display-inline"></span>
                                        <span id="slug_edit" class="display-inline">
                                             <button class="btn btn-warning btn-sm slug_edit_button"> <i class="fas fa-edit"></i> </button>
                                            
                                            <input type="text" name="slug" class="form-control category_slug mt-2" value="{{$category->slug}}" style="display: none">
                                            <button class="btn btn-info btn-sm slug_update_button mt-2" style="display: none">{{__('Update')}}</button>
                                        </span>
                                    </label>
                                </div>


                                <div class="form-group">
                                    <label for="icon" class="d-block">{{__('Category Icon')}}</label>
                                    <div class="btn-group icon">
                                        <button type="button" class="btn btn-primary iconpicker-component">
                                            <i class="{{$category->icon}}"></i>
                                        </button>
                                        <button type="button" class="icp icp-dd btn btn-primary dropdown-toggle"
                                                data-selected="{{$category->icon}}" data-toggle="dropdown">
                                            <span class="caret"></span>
                                            <span class="sr-only">{{__('Toggle Dropdown')}}</span>
                                        </button>
                                        <div class="dropdown-menu"></div>
                                    </div>
                                    <input type="hidden" class="form-control" name="icon" id="edit_icon" value="{{$category->icon}}">
                                </div>

                                <div class="form-group">
                                    <label for="image">{{__('Upload Category Image')}}</label>
                                    <div class="media-upload-btn-wrapper">
                                        <div class="img-wrap">
                                            {!! render_image_markup_by_attachment_id($category->image,'','thumb') !!}
                                        </div>
                                        <input type="hidden" name="image" value="{{$category->image}}">
                                        <button type="button" class="btn btn-info media_upload_form_btn"
                                                data-btntitle="{{__('Select Image')}}"
                                                data-modaltitle="{{__('Upload Image')}}" data-toggle="modal"
                                                data-target="#media_upload_modal">
                                            {{__('Upload Image')}}
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="image">{{__('Mobile Icon Image')}}</label>
                                    <div class="media-upload-btn-wrapper">
                                        <div class="img-wrap">
                                            {!! render_image_markup_by_attachment_id($category->mobile_icon,'','thumb') !!}
                                        </div>
                                        <input type="hidden" name="mobile_icon" value="{{$category->mobile_icon}}">
                                        <button type="button" class="btn btn-info media_upload_form_btn"
                                                data-btntitle="{{__('Select Image')}}"
                                                data-modaltitle="{{__('Upload Image')}}" data-toggle="modal"
                                                data-target="#media_upload_modal">
                                            {{__('Upload Image')}}
                                        </button>
                                    </div>
                                </div>


                                <!-- meta section start -->
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <div class="card">
                                            <div class="card-body meta">
                                                <h5 class="header-title">{{__('Meta Section')}}</h5>
                                                <div class="row">
                                                    <div class="col-xl-4 col-lg-3">
                                                        <div class="nav flex-column nav-pills" id="v-pills-tab"
                                                             role="tablist" aria-orientation="vertical">
                                                            <a class="nav-link active" id="v-pills-home-tab"
                                                               data-toggle="pill" href="#v-pills-home" role="tab"
                                                               aria-controls="v-pills-home"
                                                               aria-selected="true">{{__('Category Meta')}}</a>
                                                            <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill"
                                                               href="#v-pills-profile" role="tab"
                                                               aria-controls="v-pills-profile"
                                                               aria-selected="false">{{__('Facebook Meta')}}</a>
                                                            <a class="nav-link" id="v-pills-messages-tab" data-toggle="pill"
                                                               href="#v-pills-messages" role="tab"
                                                               aria-controls="v-pills-messages"
                                                               aria-selected="false">{{__('Twitter Meta')}}</a>

                                                        </div>
                                                    </div>
                                                    <div class="col-xl-8 col-lg-9">
                                                        <div class="tab-content meta-content" id="v-pills-tabContent">
                                                            <!-- category meta section start -->
                                                            <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                                                                <ul class="nav nav-tabs mb-3" id="catMetaLangTab" role="tablist">
                                                                    <li class="nav-item">
                                                                        <a class="nav-link active" id="cat-meta-it-tab" data-toggle="tab" href="#cat-meta-it" role="tab" style="color: blue">{{__('Italian (Default)')}}</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="cat-meta-en-tab" data-toggle="tab" href="#cat-meta-en" role="tab" style="color: blue">{{__('English')}}</a>
                                                                    </li>
                                                                </ul>
                                                                <div class="tab-content">
                                                                    <div class="tab-pane fade show active" id="cat-meta-it" role="tabpanel">
                                                                        <div class="form-group">
                                                                            <label for="title">{{__('Meta Title (Italian)')}}</label>
                                                                            <input type="text" class="form-control" name="meta_title" value="{{$category->metaData->meta_title ?? ''}}" placeholder="{{__('Title')}}">
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="slug">{{__('Meta Tags (Italian)')}}</label>
                                                                            <input type="text" class="form-control" name="meta_tags" value="{{$category->metaData->meta_tags ?? ''}}" placeholder="{{ __('Slug') }}" data-role="tagsinput">
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="form-group col-md-12">
                                                                                <label for="title">{{__('Meta Description (Italian)')}}</label>
                                                                                <textarea name="meta_description" class="form-control max-height-140 meta-desc" cols="20" rows="4">  {!! $category->metaData->meta_description ?? '' !!} </textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="tab-pane fade" id="cat-meta-en" role="tabpanel">
                                                                        <div class="form-group">
                                                                            <label for="meta_title_en">{{__('Meta Title (English)')}}</label>
                                                                            <input type="text" class="form-control" name="meta_title_en" value="{{$category->metaData->meta_title_en ?? ''}}" placeholder="{{__('Title')}}">
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="meta_tags_en">{{__('Meta Tags (English)')}}</label>
                                                                            <input type="text" class="form-control" name="meta_tags_en" value="{{$category->metaData->meta_tags_en ?? ''}}" placeholder="{{ __('Slug') }}" data-role="tagsinput">
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="form-group col-md-12">
                                                                                <label for="meta_description_en">{{__('Meta Description (English)')}}</label>
                                                                                <textarea name="meta_description_en" class="form-control max-height-140 meta-desc" cols="20" rows="4">  {!! $category->metaData->meta_description_en ?? '' !!} </textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- category meta section end -->

                                                            <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                                                <ul class="nav nav-tabs mb-3" id="catFbMetaLangTab" role="tablist">
                                                                    <li class="nav-item">
                                                                        <a class="nav-link active" id="cat-fb-meta-it-tab" data-toggle="tab" href="#cat-fb-meta-it" role="tab" style="color: blue">{{__('Italian (Default)')}}</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="cat-fb-meta-en-tab" data-toggle="tab" href="#cat-fb-meta-en" role="tab" style="color: blue">{{__('English')}}</a>
                                                                    </li>
                                                                </ul>

                                                                <div class="tab-content">
                                                                    <div class="tab-pane fade show active" id="cat-fb-meta-it" role="tabpanel">
                                                                        <div class="form-group">
                                                                            <label for="title">{{__('Facebook Meta Title (Italian)')}}</label>
                                                                            <input type="text" class="form-control" data-role="tagsinput" name="facebook_meta_tags" value="{{$category->metaData->facebook_meta_tags ?? ''}}">
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="form-group col-md-12">
                                                                                <label for="title">{{__('Facebook Meta Description (Italian)')}}</label>
                                                                                <textarea name="facebook_meta_description" class="form-control max-height-140 meta-desc" cols="20" rows="4">{!! $category->metaData->facebook_meta_description ?? '' !!}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="tab-pane fade" id="cat-fb-meta-en" role="tabpanel">
                                                                        <div class="form-group">
                                                                            <label for="facebook_meta_tags_en">{{__('Facebook Meta Title (English)')}}</label>
                                                                            <input type="text" class="form-control" data-role="tagsinput" name="facebook_meta_tags_en" value="{{$category->metaData->facebook_meta_tags_en ?? ''}}">
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="form-group col-md-12">
                                                                                <label for="facebook_meta_description_en">{{__('Facebook Meta Description (English)')}}</label>
                                                                                <textarea name="facebook_meta_description_en" class="form-control max-height-140 meta-desc" cols="20" rows="4">{!! $category->metaData->facebook_meta_description_en ?? '' !!}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group ">
                                                                    <label for="og_meta_image">{{__('Facebook Meta Image (Shared)')}}</label>
                                                                    <div class="media-upload-btn-wrapper">
                                                                        <div class="img-wrap">
                                                                            {!! render_attachment_preview_for_admin($category->metaData->facebook_meta_image ?? '') !!}
                                                                        </div>
                                                                        <input type="hidden" id="facebook_meta_image" name="facebook_meta_image" value="{{$category->metaData->facebook_meta_image ?? ''}}">
                                                                        <button type="button" class="btn btn-info media_upload_form_btn" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}" data-toggle="modal" data-target="#media_upload_modal">
                                                                            {{__('Change Image')}}
                                                                        </button>
                                                                    </div>
                                                                    <small class="form-text text-muted">{{__('allowed image format: jpg,jpeg,png')}}</small>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                                                <ul class="nav nav-tabs mb-3" id="catTwMetaLangTab" role="tablist">
                                                                    <li class="nav-item">
                                                                        <a class="nav-link active" id="cat-tw-meta-it-tab" data-toggle="tab" href="#cat-tw-meta-it" role="tab" style="color: blue">{{__('Italian (Default)')}}</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="cat-tw-meta-en-tab" data-toggle="tab" href="#cat-tw-meta-en" role="tab" style="color: blue">{{__('English')}}</a>
                                                                    </li>
                                                                </ul>

                                                                <div class="tab-content">
                                                                    <div class="tab-pane fade show active" id="cat-tw-meta-it" role="tabpanel">
                                                                        <div class="form-group">
                                                                            <label for="title">{{__('Twitter Meta Tag (Italian)')}}</label>
                                                                            <input type="text" class="form-control" data-role="tagsinput" name="twitter_meta_tags" value=" {{$category->metaData->twitter_meta_tags ?? ''}}">
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="form-group col-md-12">
                                                                                <label for="title">{{__('Twitter Meta Description (Italian)')}}</label>
                                                                                <textarea name="twitter_meta_description" class="form-control max-height-140 meta-desc" cols="20" rows="4">{!! $category->metaData->twitter_meta_description ?? '' !!}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="tab-pane fade" id="cat-tw-meta-en" role="tabpanel">
                                                                        <div class="form-group">
                                                                            <label for="twitter_meta_tags_en">{{__('Twitter Meta Tag (English)')}}</label>
                                                                            <input type="text" class="form-control" data-role="tagsinput" name="twitter_meta_tags_en" value=" {{$category->metaData->twitter_meta_tags_en ?? ''}}">
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="form-group col-md-12">
                                                                                <label for="twitter_meta_description_en">{{__('Twitter Meta Description (English)')}}</label>
                                                                                <textarea name="twitter_meta_description_en" class="form-control max-height-140 meta-desc" cols="20" rows="4">{!! $category->metaData->twitter_meta_description_en ?? '' !!}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="og_meta_image">{{__('Twitter Meta Image (Shared)')}}</label>
                                                                    <div class="media-upload-btn-wrapper">
                                                                        <div class="img-wrap">
                                                                            {!! render_attachment_preview_for_admin($category->metaData->twitter_meta_image ?? '') !!}
                                                                        </div>
                                                                        <input type="hidden" id="twitter_meta_image" name="twitter_meta_image" value="{{$category->metaData->twitter_meta_image ?? ''}}">
                                                                        <button type="button" class="btn btn-info media_upload_form_btn" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}" data-toggle="modal" data-target="#media_upload_modal">
                                                                            {{__('Change Image')}}
                                                                        </button>
                                                                    </div>
                                                                    <small class="form-text text-muted">{{__('allowed image format: jpg,jpeg,png')}}</small>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- meta section end -->

                                <button type="submit" class="btn btn-primary mt-3 submit_btn">{{__('Submit')}}</button>

                              </div>
                        </form>
                   </div>
                </div>
            </div>
        </div>
    </div>
    <x-media.markup/>
@endsection
@section('script')
    <script src="{{asset('assets/backend/js/bootstrap-tagsinput.js')}}"></script>
    <script src="{{asset('assets/backend/js/jodit.fat.min.js')}}"></script>
    <x-summernote.js/>
<script>
    <x-icon-picker/> 
</script> 
<x-media.js />

<script>
    (function ($) {
        "use strict";

        $(document).ready(function () {

            //zone
            $(document).ready(function () {
                $('.zone_settings').select2();
            });

            //Permalink Code
                var sl =  $('.category_slug').val();
                var url = `{{url('/service-list/category/')}}/` + sl;
                var data = $('#slug_show').text(url).css('color', 'blue');

                function converToSlug(slug){
                   let finalSlug = slug.replace(/[^a-zA-Z0-9]/g, ' ');
                    //remove multiple space to single
                    finalSlug = slug.replace(/  +/g, ' ');
                    // remove all white spaces single or multiple spaces
                    finalSlug = slug.replace(/\s/g, '-').toLowerCase().replace(/[^\w-]+/g, '-');
                    return finalSlug;
                }

                //Slug Edit Code
                $(document).on('click', '.slug_edit_button', function (e) {
                    e.preventDefault();
                    $('.category_slug').show();
                    $(this).hide();
                    $('.slug_update_button').show();
                });

                //Slug Update Code
                $(document).on('click', '.slug_update_button', function (e) {
                    e.preventDefault();
                    $(this).hide();
                    $('.slug_edit_button').show();
                    var update_input = $('.category_slug').val();
                    var slug = converToSlug(update_input);
                    var url = `{{url('/service-list/category/')}}/` + slug;
                    $('#slug_show').text(url);
                    $('.category_slug').val(slug)
                    $('.category_slug').hide();
                });


            let jodit = null;
            let joditEn = null;
            if ($('#jodit-editor').length && !$('#jodit-editor').hasClass('jodit-initialized')) {
                $('#jodit-editor').addClass('jodit-initialized');
                jodit = Jodit.make('#jodit-editor', {
                    height: 400,
                    placeholder: '{{ __("Type Content") }}',
                    buttons: [
                        'bold', 'italic', 'underline', '|',
                        'ul', 'ol', '|',
                        'outdent', 'indent', '|',
                        'font', 'fontsize', 'brush', 'paragraph', '|',
                        'align', 'undo', 'redo', '|',
                        'link', 'image', 'video', 'table', '|',
                        'hr', 'eraser', 'fullsize'
                    ],
                    uploader: {
                        insertImageAsBase64URI: true
                    }
                });

                // Sync Jodit content with hidden textarea
                jodit.events.on('change', () => {
                    $('#description').val(jodit.getEditorValue());
                });

                // Set initial content if exists
                const initialContent = $('#description').val();
                if (initialContent && initialContent.trim() !== '') {
                    jodit.setEditorValue(initialContent);
                }
            }
            
            if ($('#jodit-editor-en').length && !$('#jodit-editor-en').hasClass('jodit-initialized')) {
                $('#jodit-editor-en').addClass('jodit-initialized');
                joditEn = Jodit.make('#jodit-editor-en', {
                    height: 400,
                    placeholder: '{{ __("Type Content (English)") }}',
                    buttons: [
                        'bold', 'italic', 'underline', '|',
                        'ul', 'ol', '|',
                        'outdent', 'indent', '|',
                        'font', 'fontsize', 'brush', 'paragraph', '|',
                        'align', 'undo', 'redo', '|',
                        'link', 'image', 'video', 'table', '|',
                        'hr', 'eraser', 'fullsize'
                    ],
                    uploader: {
                        insertImageAsBase64URI: true
                    }
                });

                // Sync Jodit content with hidden textarea
                joditEn.events.on('change', () => {
                    $('#description_en').val(joditEn.getEditorValue());
                });

                // Set initial content if exists
                const initialContentEn = $('#description_en').val();
                if (initialContentEn && initialContentEn.trim() !== '') {
                    joditEn.setEditorValue(initialContentEn);
                }
            }


        });
    })(jQuery)
</script>
@endsection 


