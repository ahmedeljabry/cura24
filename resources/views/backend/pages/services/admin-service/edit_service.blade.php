@extends('backend.admin-master')

@section('site-title')
    {{__('Edit Service')}}
@endsection
@section('style')
    <x-media.css/>
    <link rel="stylesheet" href="{{asset('assets/backend/css/jodit.fat.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/bootstrap-tagsinput.css')}}">
    <link rel="stylesheet" href="{{asset('assets/common/css/flatpickr.min.css')}}">
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40"></div>
                <x-msg.success/>
                <x-msg.error/>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="header-wrap d-flex justify-content-between">
                            <div class="left-content">
                                <h5 class="header-title">{{__('Edit Service')}}   </h5>
                            </div>
                        </div>
                        @if(config('services.google_translate.enabled') && (filled(config('services.google_translate.api_key')) || filled(config('services.google_translate.project_id'))))
                            <div class="alert alert-info mt-3 mb-0">
                                {{ __('Google Translate is enabled. Title, description, and meta fields will be translated to Italian when you save this service.') }}
                            </div>
                        @endif
                        <form action="{{route('admin.edit.service',$service->id)}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="single-dashboard-input">
                                <div class="single-info-input margin-top-30">
                                    <label for="category" class="info-title"> {{__('Select Main Category*')}} </label>
                                    <select name="category" id="category" class="form-control">
                                        <option value="">{{__('Select Category')}}</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" @if($cat->id==$service->category_id) selected @endif>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="single-info-input margin-top-30">
                                    <label for="subcategory" class="info-title"> {{__('Select Sub Category')}} </label>
                                    <select  name="subcategory" id="subcategory" class="subcategory form-control">
                                        <option @if(!empty( $service->subcategory_id)) value="{{ $service->subcategory_id }}"  @else value="" @endif>{{ optional($service->subcategory)->name }}</option>
                                    </select>
                                </div>

                                <div class="single-info-input margin-top-30 child_category_wrapper">
                                    <label for="child_category" class="info-title"> {{__('Select Child Category')}} </label>
                                    <select  name="child_category" id="child_category" class="child_category form-control">
                                        <option @if(!empty( $service->child_category_id)) value="{{ $service->child_category_id }}"  @else value="" @endif>{{ optional($service->childcategory)->name }}</option>
                                    </select>
                                </div>

                            </div>

                            <div class="single-dashboard-input">
                                <div class="single-info-input margin-top-30">
                                    <label for="seller_id" class="info-title"> {{__('Select Seller*')}} </label>
                                    <select name="seller_id" id="seller_id" class="form-control">
                                        <option value="">{{__('Select Seller')}}</option>
                                        @foreach($sellers as $seller)
                                            <option value="{{ $seller->id }}"  @if($seller->id==$service->seller_id) selected @endif>{{ $seller->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="single-info-input margin-top-30 mt-5">
                                    <label for="video" class="info-title"> {{__('Service Video Url')}} </label>
                                    <input class="form-control" name="video" id="video" value="{{ $service->video }}" type="text" placeholder="{{__('youtube embed code')}}">
                                    <small class="text-danger">{{__('must be embed code from youtube.')}}</small>
                                </div>
                            </div>

                            <div class="single-dashboard-input mt-3">
                                <div class="single-info-input margin-top-30 permalink_label">
                                    <label for="title" class="info-title text-dark"> {{__('Permalink*')}} </label>
                                    <span id="slug_show" class="display-inline"></span>
                                    <span id="slug_edit" class="display-inline"> </span>
                                         <button class="btn btn-warning btn-sm slug_edit_button">  <i class="las la-edit"></i> </button>
                                          <input class="form-control service_slug" name="slug" id="slug" style="display: none" type="text" value="{{$service->slug}}">
                                          <button class="btn btn-info btn-sm slug_update_button mt-2" style="display: none">{{__('Update')}}</button>
                                </div>
                            </div>
                            <div class="card mb-4 mt-4">
                                <div class="card-header bg-transparent border-bottom-0">
                                    <ul class="nav nav-tabs" id="serviceLangTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="service-it-tab" data-toggle="tab" href="#service-it" role="tab" aria-controls="service-it" aria-selected="true" style="color: blue">{{__('Italian (Default)')}}</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="service-en-tab" data-toggle="tab" href="#service-en" role="tab" aria-controls="service-en" aria-selected="false" style="color: blue">{{__('English')}}</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="serviceLangTabContent">
                                        <!-- Italian Tab -->
                                        <div class="tab-pane fade show active" id="service-it" role="tabpanel" aria-labelledby="service-it-tab">
                                            <div class="single-dashboard-input">
                                                <div class="single-info-input mt-3">
                                                    <label for="title" class="info-title"> {{__('Service Title (Italian)*')}} </label>
                                                    <input class="form-control" name="title" id="title" value="{{ $service->title }}" type="text" placeholder="{{__('Add title')}}">
                                                </div>
                                            </div>
                                            <div class="single-dashboard-input">
                                                <div class="single-info-input margin-top-30">
                                                    <label for="description" class="info-title"> {{__('Service Description (Italian)*')}} <span class="text-danger">{{ __('minimum 150 characters.') }}</span> </label>
                                                    <textarea id="jodit-editor" style="height: 400px;"></textarea>
                                                    <!-- Hidden textarea for form submission -->
                                                    <textarea name="description" id="description" class="d-none">{{ $service->description }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- English Tab -->
                                        <div class="tab-pane fade" id="service-en" role="tabpanel" aria-labelledby="service-en-tab">
                                            <div class="single-dashboard-input">
                                                <div class="single-info-input mt-3">
                                                    <label for="title_en" class="info-title"> {{__('Service Title (English)')}} </label>
                                                    <input class="form-control" name="title_en" id="title_en" value="{{ $service->title_en }}" type="text" placeholder="{{__('Add title (English)')}}">
                                                </div>
                                            </div>
                                            <div class="single-dashboard-input">
                                                <div class="single-info-input margin-top-30">
                                                    <label for="description_en" class="info-title"> {{__('Service Description (English)')}} <span class="text-danger">{{ __('minimum 150 characters.') }}</span> </label>
                                                    <textarea id="jodit-editor-en" style="height: 400px;"></textarea>
                                                    <textarea name="description_en" id="description_en" class="d-none">{{ $service->description_en }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-dashboard-input">
                                <div class="single-info-input margin-top-30">
                                    <div class="form-group">
                                        <div class="media-upload-btn-wrapper">
                                            <div class="img-wrap">
                                                {!! render_image_markup_by_attachment_id($service->image,'','thumb') !!}
                                            </div>
                                            <input type="hidden" id="image" name="image"
                                                   value="{{$service->image}}">
                                            <button type="button" class="btn btn-info media_upload_form_btn"
                                                    data-btntitle="{{__('Select Image')}}"
                                                    data-modaltitle="{{__('Upload Image')}}" data-toggle="modal"
                                                    data-target="#media_upload_modal">
                                                {{__('Upload Service Image')}}
                                            </button>
                                        </div>
                                        <small class="form-text text-muted">{{__('allowed image format: jpg,jpeg,png')}}</small>
                                        <small class="text-danger">{{ __('recommended size 1394x315') }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group ">
                                <div class="media-upload-btn-wrapper">
                                    <div class="img-wrap">
                                        {!! render_gallery_image_attachment_preview($service->image_gallery ?? '') !!}
                                    </div>
                                    <input type="hidden" name="image_gallery">
                                    <button type="button" class="btn btn-info media_upload_form_btn"
                                            data-btntitle="{{__('Select Image')}}"
                                            data-modaltitle="{{__('Upload Image')}}"
                                            data-toggle="modal"
                                            data-mulitple="true"
                                            data-target="#media_upload_modal">
                                        {{__('Upload Gallery Image')}}
                                    </button>
                                    <small>{{ __('image format: jpg,jpeg,png')}}</small> <br>
                                    <small>{{ __('recommended size 730x497') }}</small>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body meta">
                                            <h5 class="header-title">{{__('Meta Section')}}</h5>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <div class="nav flex-column nav-pills" id="v-pills-tab"
                                                         role="tablist" aria-orientation="vertical">
                                                        <a class="nav-link active" id="v-pills-home-tab"
                                                           data-toggle="pill" href="#v-pills-home" role="tab"
                                                           aria-controls="v-pills-home"
                                                           aria-selected="true">{{__('Service Meta')}}</a>
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
                                                <div class="col-lg-9">
                                                    <div class="tab-content meta-content" id="v-pills-tabContent">

                                                        <div class="tab-pane fade show active" id="v-pills-home"
                                                             role="tabpanel" aria-labelledby="v-pills-home-tab">
                                                            <div class="form-group">
                                                            <div class="form-group">
                                                                <label for="title">{{__('Meta Title (Italian)')}}</label>
                                                                <input type="text" class="form-control" name="meta_title"
                                                                       value="{{$service->metaData->meta_title ?? ''}}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="title_en">{{__('Meta Title (English)')}}</label>
                                                                <input type="text" class="form-control" name="meta_title_en"
                                                                       value="{{$service->metaData->meta_title_en ?? ''}}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="slug">{{__('Meta Tags (Italian)')}}</label>
                                                                <input type="text" class="form-control"  data-role="tagsinput" name="meta_tags"
                                                                       value="{{$service->metaData->meta_tags ?? ''}}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="slug_en">{{__('Meta Tags (English)')}}</label>
                                                                <input type="text" class="form-control"  data-role="tagsinput" name="meta_tags_en"
                                                                       value="{{$service->metaData->meta_tags_en ?? ''}}">
                                                            </div>

                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="title">{{__('Meta Description (Italian)')}}</label>
                                                                    <textarea name="meta_description"
                                                                              class="form-control max-height-140"
                                                                              cols="20"
                                                                              rows="4">{!! $service->metaData->meta_description ?? '' !!}</textarea>
                                                                </div>
                                                                <div class="form-group col-md-12">
                                                                    <label for="title_en">{{__('Meta Description (English)')}}</label>
                                                                    <textarea name="meta_description_en"
                                                                              class="form-control max-height-140"
                                                                              cols="20"
                                                                              rows="4">{!! $service->metaData->meta_description_en ?? '' !!}</textarea>
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
                                                             aria-labelledby="v-pills-profile-tab">
                                                            <div class="form-group">
                                                                <label for="title">{{__('Facebook Meta Title (Italian)')}}</label>
                                                                <input type="text" class="form-control" placeholder="{{__('Title')}}"
                                                                       name="facebook_meta_tags" value="{{$service->metaData->facebook_meta_tags ?? ''}}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="title_en">{{__('Facebook Meta Title (English)')}}</label>
                                                                <input type="text" class="form-control" placeholder="{{__('Title (English)')}}"
                                                                       name="facebook_meta_tags_en" value="{{$service->metaData->facebook_meta_tags_en ?? ''}}">
                                                            </div>

                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="title">{{__('Facebook Meta Description (Italian)')}}</label>
                                                                    <textarea name="facebook_meta_description"
                                                                              class="form-control max-height-140 meta-desc"
                                                                              cols="20"
                                                                              rows="4">{!! $service->metaData->facebook_meta_description ?? '' !!}</textarea>
                                                                </div>
                                                                <div class="form-group col-md-12">
                                                                    <label for="title_en">{{__('Facebook Meta Description (English)')}}</label>
                                                                    <textarea name="facebook_meta_description_en"
                                                                              class="form-control max-height-140 meta-desc"
                                                                              cols="20"
                                                                              rows="4">{!! $service->metaData->facebook_meta_description_en ?? '' !!}</textarea>
                                                                </div>
                                                            </div>

                                                            <div class="form-group ">
                                                                <label for="og_meta_image">{{__('Facebook Meta Image')}}</label>
                                                                <div class="media-upload-btn-wrapper">
                                                                    <div class="img-wrap">
                                                                        {!! render_attachment_preview_for_admin($service->metaData->facebook_meta_image ?? '') !!}
                                                                    </div>
                                                                    <input type="hidden" id="facebook_meta_image" name="facebook_meta_image"
                                                                           value="{{$service->metaData->facebook_meta_image ?? ''}}">
                                                                    <button type="button" class="btn btn-info media_upload_form_btn"
                                                                            data-btntitle="{{__('Select Image')}}"
                                                                            data-modaltitle="{{__('Upload Image')}}" data-toggle="modal"
                                                                            data-target="#media_upload_modal">
                                                                        {{__('Change Image')}}
                                                                    </button>
                                                                </div>
                                                                <small class="form-text text-muted">{{__('allowed image format: jpg,jpeg,png')}}</small>
                                                            </div>
                                                        </div>

                                                        <div class="tab-pane fade" id="v-pills-messages" role="tabpanel"
                                                             aria-labelledby="v-pills-messages-tab">
                                                            <div class="form-group">
                                                                <label for="title">{{__('Twitter Meta Title (Italian)')}}</label>
                                                                <input type="text" class="form-control" placeholder="{{__('Title')}}"
                                                                       name="twitter_meta_tags" value=" {{$service->metaData->twitter_meta_tags ?? ''}}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="title_en">{{__('Twitter Meta Title (English)')}}</label>
                                                                <input type="text" class="form-control" placeholder="{{__('Title (English)')}}"
                                                                       name="twitter_meta_tags_en" value=" {{$service->metaData->twitter_meta_tags_en ?? ''}}">
                                                            </div>

                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="title">{{__('Twitter Meta Description (Italian)')}}</label>
                                                                    <textarea name="twitter_meta_description"
                                                                              class="form-control max-height-140 meta-desc"
                                                                              cols="20"
                                                                              rows="4">{!! $service->metaData->twitter_meta_description ?? '' !!}</textarea>
                                                                </div>
                                                                <div class="form-group col-md-12">
                                                                    <label for="title_en">{{__('Twitter Meta Description (English)')}}</label>
                                                                    <textarea name="twitter_meta_description_en"
                                                                              class="form-control max-height-140 meta-desc"
                                                                              cols="20"
                                                                              rows="4">{!! $service->metaData->twitter_meta_description_en ?? '' !!}</textarea>
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="og_meta_image">{{__('Twitter Meta Image')}}</label>
                                                                <div class="media-upload-btn-wrapper">
                                                                    <div class="img-wrap">
                                                                        {!! render_attachment_preview_for_admin($service->metaData->twitter_meta_image ?? '') !!}
                                                                    </div>
                                                                    <input type="hidden" id="twitter_meta_image" name="twitter_meta_image"
                                                                           value="{{$service->metaData->twitter_meta_image ?? ''}}">
                                                                    <button type="button" class="btn btn-info media_upload_form_btn"
                                                                            data-btntitle="{{__('Select Image')}}"
                                                                            data-modaltitle="{{__('Upload Image')}}" data-toggle="modal"
                                                                            data-target="#media_upload_modal">
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


                            {{-- City & Area Availability --}}
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="header-title">{{__('Service Availability')}}</h5>
                                            <div class="d-flex align-items-center gap-3 mt-3">
                                                <label class="fw-semibold mb-0">{{__('Available in All Cities')}}</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                           id="is_service_all_cities_admin"
                                                           name="is_service_all_cities"
                                                           value="1"
                                                           @if($service->is_service_all_cities) checked @endif>
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mt-1">{{__('Turn ON to make this service available in all cities. Turn OFF to select specific cities and areas.')}}</small>

                                            {{-- City/Area Checklist --}}
                                            <div id="admin_city_area_selector" class="mt-4" style="display:none;">
                                                <h6 class="fw-semibold mb-3">{{__('Select Available Cities & Areas')}}</h6>
                                                @if($cities->isEmpty())
                                                    <div class="alert alert-warning">{{__('No cities found. Please add cities from Location settings.')}}</div>
                                                @else
                                                    <div class="row g-3">
                                                        @foreach($cities as $city)
                                                            @php $cityAreas = $areas->where('service_city_id', $city->id); @endphp
                                                            <div class="col-lg-4 col-md-6">
                                                                <div class="card border rounded p-3 h-100">
                                                                    <div class="form-check mb-2">
                                                                        <input class="form-check-input admin-city-check"
                                                                               type="checkbox"
                                                                               name="service_cities[]"
                                                                               value="{{ $city->id }}"
                                                                               id="admin_city_{{ $city->id }}"
                                                                               @if($service->service_city_id == $city->id) checked @endif>
                                                                        <label class="form-check-label fw-bold" for="admin_city_{{ $city->id }}">
                                                                            {{ $city->service_city }}
                                                                        </label>
                                                                    </div>
                                                                    @if($cityAreas->isNotEmpty())
                                                                        <div class="ms-3 admin-city-areas-{{ $city->id }}">
                                                                            @foreach($cityAreas as $area)
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input admin-area-check-{{ $city->id }}"
                                                                                           type="checkbox"
                                                                                           name="service_areas[]"
                                                                                           value="{{ $area->id }}"
                                                                                           id="admin_area_{{ $area->id }}"
                                                                                           @if($service->service_area_id == $area->id) checked @endif>
                                                                                    <label class="form-check-label" for="admin_area_{{ $area->id }}">
                                                                                        {{ $area->service_area }}
                                                                                    </label>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    @else
                                                                        <p class="text-muted ms-3 mb-0" style="font-size:12px;">{{__('No areas listed')}}</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="btn-wrapper margin-top-40">
                                <input type="submit" class="btn btn-success btn-bg-1" value="{{__('Save & Next')}} ">
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
    <x-media.js />
    <script src="{{asset('assets/backend/js/jodit.fat.min.js')}}"></script>
    <script src="{{asset('assets/backend/js/bootstrap-tagsinput.js')}}"></script>
    <script src="{{asset('assets/common/js/flatpickr.js')}}"></script>
    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {
                // City/Area toggle
                function toggleAdminCitySelector() {
                    if ($('#is_service_all_cities_admin').is(':checked')) {
                        $('#admin_city_area_selector').hide();
                    } else {
                        $('#admin_city_area_selector').show();
                    }
                }
                toggleAdminCitySelector();
                $('#is_service_all_cities_admin').on('change', toggleAdminCitySelector);

                // City master check selects/deselects all areas below it
                $(document).on('change', '.admin-city-check', function () {
                    var cityId = $(this).val();
                    var checked = $(this).is(':checked');
                    $('.admin-area-check-' + cityId).prop('checked', checked);
                });


                //Permalink Code
                $('.permalink_label').hide();

                $(document).on('keyup', '#title', function (e) {
                    var slug = converToSlug($(this).val());
                    var url = "{{url('/service/')}}/" + slug;
                    $('.permalink_label').show();
                    var data = $('#slug_show').text(url).css('color', '#3c3cf7');
                    $('.service_slug').val(slug);

                });

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
                    $('.service_slug').show();
                    $(this).hide();
                    $('.slug_update_button').show();
                });

                //Slug Update Code
                $(document).on('click', '.slug_update_button', function (e) {
                    e.preventDefault();
                    $(this).hide();
                    $('.slug_edit_button').show();
                    var update_input = $('.service_slug').val();
                    var slug = converToSlug(update_input);
                    var url = `{{url('/service/')}}/` + slug;
                    $('#slug_show').text(url);
                    $('.service_slug').hide();
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

                $('#category').on('change',function(){
                    let category_id = $(this).val();
                    $.ajax({
                        method:'post',
                        url:"{{route('admin.get.subcategory')}}",
                        data:{category_id:category_id},
                        success:function(res){
                            if(res.status=='success'){
                                let alloptions = "<option value=''>{{__('Select SubCategory')}}</option>";
                                let allSubCategory = res.sub_categories;
                                $.each(allSubCategory,function(index,value){
                                    alloptions +="<option value='" + value.id + "'>" + value.name + "</option>";
                                });
                                $(".subcategory").html(alloptions);
                                $('#subcategory').niceSelect('update');
                            }
                        }
                    })
                })

                    // child category edit get
                    $(document).on('click','#subcategory', function() {
                    let sub_cat_id = $(this).val();
                    $.ajax({
                        method:'post',
                        url:"{{route('admin.get.subcategory.with.child.category')}}",
                        data:{sub_cat_id:sub_cat_id},
                        success:function(res){
                            if (res.status == 'success') {
                                var alloptions = "<option value=''>{{__('Select Child Category')}}</option>";
                                var allList = "<li data-value='' class='option'>{{__('Select Child Category')}}</li>";
                                var allChildCategory = res.child_category;

                                $.each(allChildCategory, function(index, value) {
                                    alloptions += "<option value='" + value.id +
                                        "'>" + value.name + "</option>";
                                    allList += "<li class='option' data-value='" + value.id +
                                        "'>" + value.name + "</li>";
                                });

                                $("#child_category").html(alloptions);
                                $(".child_category_wrapper ul.list").html(allList);
                                $(".child_category_wrapper").find(".current").html("Select Child Category");
                            }
                        }
                    })
                })
            });
        })(jQuery)
    </script>
@endsection

