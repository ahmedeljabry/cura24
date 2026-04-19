@extends('backend.admin-master')
@section('style')
<x-media.css/>
<link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
@endsection
@section('site-title')
    {{__('SEO Settings')}}
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-12 mt-5">
               <x-msg.success/>
               <x-msg.error/>
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__("SEO Settings")}}</h4>
                        <form action="{{route('admin.general.seo.settings')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card mb-4">
                                <div class="card-header bg-transparent border-bottom-0">
                                    <ul class="nav nav-tabs" id="seoLangTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="seo-it-tab" data-toggle="tab" href="#seo-it" role="tab" aria-controls="seo-it" aria-selected="true" style="color: blue">{{__('Italian (Default)')}}</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="seo-en-tab" data-toggle="tab" href="#seo-en" role="tab" aria-controls="seo-en" aria-selected="false" style="color: blue">{{__('English')}}</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="seoLangTabContent">
                                        <!-- Italian Tab -->
                                        <div class="tab-pane fade show active" id="seo-it" role="tabpanel" aria-labelledby="seo-it-tab">
                                            <div class="form-group">
                                                <label for="site_meta_tags">{{__('Site Meta Tags (Italian)')}}</label>
                                                <input type="text" name="site_meta_tags"  class="form-control" data-role="tagsinput" value="{{get_static_option('site_meta_tags')}}" id="site_meta_tags">
                                            </div>
                                            <div class="form-group">
                                                <label for="site_meta_description">{{__('Site Meta Description (Italian)')}}</label>
                                                <textarea name="site_meta_description"  class="form-control" id="site_meta_description">{{get_static_option('site_meta_description')}}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="og_meta_title">{{__('Og Meta Title (Italian)')}}</label>
                                                <input type="text" name="og_meta_title"  class="form-control"  value="{{get_static_option('og_meta_title')}}" id="og_meta_title">
                                            </div>
                                            <div class="form-group">
                                                <label for="og_meta_description">{{__('Og Meta Description (Italian)')}}</label>
                                                <textarea name="og_meta_description"  class="form-control" id="og_meta_description">{{get_static_option('og_meta_description')}}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="og_meta_site_name">{{__('Og Meta Site Name (Italian)')}}</label>
                                                <input type="text" name="og_meta_site_name"  class="form-control"  value="{{get_static_option('og_meta_site_name')}}" id="og_meta_site_name">
                                            </div>
                                            <div class="form-group">
                                                <label for="og_meta_url">{{__('Og Meta URL (Italian)')}}</label>
                                                <input type="text" name="og_meta_url"  class="form-control"  value="{{get_static_option('og_meta_url')}}" id="og_meta_url">
                                            </div>
                                        </div>

                                        <!-- English Tab -->
                                        <div class="tab-pane fade" id="seo-en" role="tabpanel" aria-labelledby="seo-en-tab">
                                            <div class="form-group">
                                                <label for="site_meta_tags_en">{{__('Site Meta Tags (English)')}}</label>
                                                <input type="text" name="site_meta_tags_en"  class="form-control" data-role="tagsinput" value="{{get_static_option('site_meta_tags_en')}}" id="site_meta_tags_en">
                                            </div>
                                            <div class="form-group">
                                                <label for="site_meta_description_en">{{__('Site Meta Description (English)')}}</label>
                                                <textarea name="site_meta_description_en"  class="form-control" id="site_meta_description_en">{{get_static_option('site_meta_description_en')}}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="og_meta_title_en">{{__('Og Meta Title (English)')}}</label>
                                                <input type="text" name="og_meta_title_en"  class="form-control"  value="{{get_static_option('og_meta_title_en')}}" id="og_meta_title_en">
                                            </div>
                                            <div class="form-group">
                                                <label for="og_meta_description_en">{{__('Og Meta Description (English)')}}</label>
                                                <textarea name="og_meta_description_en"  class="form-control" id="og_meta_description_en">{{get_static_option('og_meta_description_en')}}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="og_meta_site_name_en">{{__('Og Meta Site Name (English)')}}</label>
                                                <input type="text" name="og_meta_site_name_en"  class="form-control"  value="{{get_static_option('og_meta_site_name_en')}}" id="og_meta_site_name_en">
                                            </div>
                                            <div class="form-group">
                                                <label for="og_meta_url_en">{{__('Og Meta URL (English)')}}</label>
                                                <input type="text" name="og_meta_url_en"  class="form-control"  value="{{get_static_option('og_meta_url_en')}}" id="og_meta_url_en">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                                        <div class="form-group">
                                            <label for="og_meta_image">{{__('Og Meta Image Image')}}</label>
                                            <div class="media-upload-btn-wrapper">
                                                <div class="img-wrap">
                                                    @php
                                                        $og_meta_image = get_attachment_image_by_id(get_static_option('og_meta_image'),null,true);
                                                        $og_meta_image_btn_label =__( 'Upload Image');
                                                    @endphp
                                                    @if (!empty($og_meta_image))
                                                        <div class="attachment-preview">
                                                            <div class="thumbnail">
                                                                <div class="centered">
                                                                    <img class="avatar user-thumb" src="{{$og_meta_image['img_url']}}" alt="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @php  $site_breadcrumb_bg_btn_label = __('Change Image'); @endphp
                                                    @endif
                                                </div>
                                                <input type="hidden" id="og_meta_image" name="og_meta_image" value="{{get_static_option('og_meta_image')}}">
                                                <button type="button" class="btn btn-info media_upload_form_btn" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}" data-toggle="modal" data-target="#media_upload_modal">
                                                    {{__($site_breadcrumb_bg_btn_label)}}
                                                </button>
                                            </div>
                                            <small class="form-text text-muted">{{__('allowed image format: jpg,jpeg,png, Recommended image size 1920x600')}}</small>
                                        </div>
                            <button id="update" type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update Changes')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-media.markup/>
@endsection
@section('script')
<script src="{{asset('assets/backend/js/dropzone.js')}}"></script>
<x-media.js/>
    <script src="{{asset('assets/backend/js/bootstrap-tagsinput.js')}}"></script>
    <script>
        <x-btn.update/>
    </script>
@endsection
