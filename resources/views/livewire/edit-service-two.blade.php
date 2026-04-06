<form enctype="multipart/form-data" action="{{ route('seller.edit.services', $services->id) }}" method="POST" id="service-form">
    @csrf
    <input type="hidden" name="current_tab" wire:model="current_tab">
    <input type="hidden" name="is_service_all_cities"  id="is_service_all_cities_id">
    <div class="add-service-wrapper mt-4">
        <!--Nav Bar Tabs markup start -->
        <div wire:ignore class="nav nav-pills" id="add-service-tab"
             role="tablist" aria-orientation="vertical">
            <a class="nav-link @if($current_tab === "service-info-tab") active @endif  stepIndicator" id="service-info-tab"
               data-bs-toggle="pill" href="#service-info" role="tab"
               aria-controls="service-info"
               aria-selected="true"><span class="nav-link-number">{{ __('1') }}</span>{{__('Service Info')}}</a>
            <a class="nav-link @if($current_tab === "service-category-tab") active @endif  stepIndicator" id="service-category-tab"
               data-bs-toggle="pill" href="#service-category" role="tab"
               aria-controls="service-category"
               aria-selected="true"><span class="nav-link-number">{{ __('2') }}</span>{{__('Service Category')}}</a>
            <a class="nav-link @if($current_tab === "service-attribute-tab")  active @endif stepIndicator" id="service-attribute-tab" data-bs-toggle="pill"
               href="#service-attribute" role="tab"
               aria-controls="service-attribute"
               aria-selected="false"><span class="nav-link-number">{{ __('3') }}</span>{{__('Service Attributes')}}</a>
            <a class="nav-link  @if($current_tab === "services-meta-tab") active @endif  stepIndicator" id="services-meta-tab" data-bs-toggle="pill"
               href="#services-meta" role="tab"
               aria-controls="services-meta"
               aria-selected="false"><span class="nav-link-number">{{ __('4') }}</span>{{__('Meta Section')}}</a>
            <a class="nav-link @if($current_tab === "service-media-uploads-tab") active @endif  stepIndicator" id="service-media-uploads-tab" data-bs-toggle="pill"
               href="#service-media-uploads" role="tab"
               aria-controls="service-media-uploads"
               aria-selected="false"><span class="nav-link-number">{{ __('5') }}</span>{{__('Media Uploads')}}</a>
            <a class="nav-link @if($current_tab === "service-set-availability-tab") active @endif stepIndicator" id="service-set-availability-tab" data-bs-toggle="pill"
               href="#service-set-availability" role="tab"
               aria-controls="service-set-availability"
               aria-selected="false"><span class="nav-link-number">{{ __('6') }}</span>{{__('Set Availability')}}</a>
        </div>
        <!--Nav Bar Tabs markup end -->
        <div  class="add-service-content-wrapper mt-4">
            <div class="tab-content add-service-content" id="add-service-tabContent">

                <!-- service Info start -->
                <div wire:ignore class="tab-pane fade @if($current_tab === 'service-info-tab') show active @endif step" id="service-info" role="tabpanel" aria-labelledby="service-info-tab">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="single-dashboard-input">
                                <div class="row g-4">
                                    <div class="col-sm-6">
                                        <div class="single-info-input">
                                            <label for="title" class="info-title">{{ __('Service Title') }} <span class="text-danger">*</span></label>
                                            <input class="form--control" id="title" type="text" name="title" placeholder="{{ __('Add title') }}" value="{{ $services->title }}">
                                            @error('title')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="single-dashboard-input" wire:ignore>
                                            <div class="single-info-input margin-top-30 permalink_label">
                                                <label for="slug" class="info-title text-dark">{{ __('Permalink') }} <span class="text-danger">*</span></label>
                                                <span id="slug_show" style="color: blue;">{{ url('/service-list/') }}/<span id="slug_text">{{ $services->slug }}</span></span>
                                                <span id="slug_edit" class="display-inline"></span>
                                                <button type="button" class="btn btn-warning btn-sm slug_edit_button"><i class="las la-edit"></i></button>
                                                <input class="form--control service_slug" id="slug" style="display: none" type="text" name="slug" value="{{ $services->slug }}">
                                                <button type="button" class="btn btn-info btn-sm slug_update_button mt-2" style="display: none">{{ __('Update') }}</button>
                                                @error('slug')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" wire:ignore>
                                        <div class="single-info-input">
                                            <label for="video" class="info-title">{{ __('Service Video URL') }}</label>
                                            <input class="form--control" id="video" type="text" name="video" placeholder="{{ __('Youtube embed code') }}" value="{{ $services->video }}">
                                            <small class="text-danger">{{ __('Must be embed code from Youtube.') }} <span class="text-dark">{{ __('Ex. <iframe width="560" height="315" src="https://www.youtube.com"></iframe>') }}</span></small>
                                            @error('video')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12">
                                        <div class="single-info-input">
                                            <label for="description" class="info-title">{{ __('Service Description') }} <span class="text-danger">*</span> <small class="text-info">{{ __('Minimum 150 characters') }}</small></label>
                                            <!-- Jodit Editor Container -->
                                            <textarea id="jodit-editor" style="height: 200px;"></textarea>
                                            <!-- Hidden textarea for form submission -->
                                            <textarea name="description" id="description" class="form-control d-none">{!! $services->description !!}</textarea>
                                            @error('description')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- service Info end-->

                <!-- service Category start-->
                <div wire:ignore class="tab-pane fade @if($current_tab === "service-category-tab") show active @endif step" id="service-category" role="tabpanel" aria-labelledby="service-category-tab">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="single-dashboard-input">
                                <div class="row g-4">
                                    <div class="col-lg-4 col-sm-6">
                                        <div class="single-info-input">
                                            <label for="category" class="info-title"> {{__('Select Main Category')}} <span class="text-danger">*</span> </label>
                                            <select id="category" class="category" name="category" wire:model.defer="category">
                                                <option value="">{{ __('Select Category') }}</option>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}" @if($cat->id == $services['category_id']) selected @endif> {{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6">
                                        <div class="single-info-input sub_category_wrapper">
                                            <label for="subcategory" class="info-title"> {{__('Select Sub Category')}} </label>
                                            <select id="subcategory" class="subcategory" name="subcategory" wire:model.defer="subcategory">
                                                <option @if(!empty($services['subcategory_id'])) value="{{ $services['subcategory_id'] }}" @else value="" @endif>
                                                    {{ optional($services->subcategory)->name }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6">
                                        <div class="single-info-input child_category_wrapper">
                                            <label for="child_category" class="info-title"> {{__('Select Child Category')}} </label>
                                            <select name="child_category" id="child_category" wire:model.defer="child_category">
                                                <option @if(!empty($services->child_category_id)) value="{{ $services->child_category_id }}" @else value="" @endif>
                                                    {{ optional($services->childcategory)->name }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- service Category end-->

                <!-- service attribute start-->
                <div wire:ignore.self class="tab-pane fade service_tab_hide_show
                     @if($current_tab === "service-attribute-tab") show active @endif step" id="service-attribute"   role="tabpanel" aria-labelledby="service-attribute-tab">
                    <div class="row g-4">
                        <div wire:ignore class="col-xxl-2 col-md-3 col-sm-4" >
                            <div class="service-attribute-wrapper">
                                <!-- incliude service tabs markup start -->
                                <div class="nav nav-pills flex-column" id="add-service-attribute-tab"
                                     role="tablist" aria-orientation="vertical">
                                    <a class="nav-link active" id="included-service-tab"
                                       data-bs-toggle="pill" href="#included-service" role="tab"
                                       aria-controls="included-service"
                                       aria-selected="true">
                                        <span class="nav-link-number">{{__('1')}}</span>
                                        {{__('Included Service')}}</a>
                                    <a class="nav-link" id="additional-service-tab" data-bs-toggle="pill"
                                       href="#additional-service" role="tab"
                                       aria-controls="additional-service"
                                       aria-selected="false">
                                        <span class="nav-link-number">{{__('2')}}</span>
                                        {{__('Additional Service')}}</a>
                                    <a class="nav-link" id="benefit-service-tab" data-bs-toggle="pill"
                                       href="#benefit-service" role="tab"
                                       aria-controls="benefit-service"
                                       aria-selected="false">
                                        <span class="nav-link-number">{{__('3')}}</span>
                                        {{__('Benefit')}}</a>
                                    <a class="nav-link faq_show_hide" id="faq-service-tab" data-bs-toggle="pill" href="#faq-service" role="tab" aria-controls="faq-service" aria-selected="false">
                                        <span class="nav-link-number">{{__('4')}}</span>
                                        {{__('Faq')}}
                                    </a>
                                </div>
                                <!-- include service tabs markup end -->

                                <!--service price show start -->
                                <div class="edit-service-wrappers mt-4  ">
                                    <div class="single-dashboard-input service-price-show-hide">
                                        <div class="single-info-input">
                                            <label class="info-title"> {{__('Service Price')}}</label>
                                            <input class="form--control" name="service_total_price" type="text" value="{{ $services->price }}" id="service_total_price" readonly>
                                        </div>
                                    </div>
                                </div>
                                <!--service price show end -->
                            </div>
                        </div>
                        <div class="col-xxl-10 col-md-9 col-sm-8">
                            <div class="row g-4">
                                <div class="col-xl-12">
                                    <div class="tab-content add-service-attribute-content" id="add-service-attribute-tabContent">

                                        <div class="tab-pane fade active show" id="included-service" role="tabpanel" aria-labelledby="included-service-tab">
                                            <!-- Include Service start -->
                                            <div class="single-settings">
                                                <div class="dashboard_table__title__flex">
                                                    <div class="dashboard__headerContents__left">
                                                        <h4 class="input-title">  {{__('Whats Included This Package')}} </h4>
                                                        <div class="online_service mt-3">

                                                            <div class="dashboard-switch-single d-flex gap-1">
                                                                <span class="text-info">{{ __('Is Service Online') }}</span>
                                                                <input type="hidden" name="is_service_online" value="0">
                                                                <input class="custom-switch is_service_online" id="is_service_online"
                                                                       type="checkbox" name="is_service_online" value="1"
                                                                        {{ $services->is_service_online ? 'checked' : '' }} />
                                                                <label class="switch-label mt-0" for="is_service_online"></label>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- include markup start -->
                                                <div>
                                                    <div class="include-services-container add-input append-additional-includes mt-4">
                                                        @foreach($include_service_inputs as $key => $value)
                                                            <div wire:ignore.self class="single-dashboard-input what-include-element mt-4 include-service-field" data-index="{{ $key }}">
                                                                <div class="row align-items-center g-4">
                                                                    <div class="col-lg-4 col-sm-6">
                                                                        <div class="single-info-input">
                                                                            <label class="label_title">{{ __('Title') }} <span class="text-danger">*</span></label>
                                                                            <input class="form--control" type="text" name="include_service_inputs[{{ $key }}][include_service_title]" placeholder="{{ __('Service title') }}" value="{{ $value['include_service_title'] ?? '' }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="@if($is_service_online === true || ($is_service_online === null && $services->is_service_online == 1)) d-none @endif col-lg-3 col-sm-6 is_service_online_hide">
                                                                        <div class="single-info-input">
                                                                            <label class="label_title">{{ __('Unit Price') }} <span class="text-danger">*</span></label>
                                                                            <input class="form--control include-price" type="text" name="include_service_inputs[{{ $key }}][include_service_price]" placeholder="{{ __('Add Price') }}" value="{{ $value['include_service_price'] ?? '' }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="@if($is_service_online === true || ($is_service_online === null && $services->is_service_online == 1)) d-none @endif col-lg-3 col-sm-6 is_service_online_hide">
                                                                        <div class="single-info-input">
                                                                            <label class="label_title">{{ __('Quantity') }}</label>
                                                                            <input class="form--control numeric-value" name="include_service_inputs[{{ $key }}][include_service_quantity]" value="{{ $value['include_service_quantity'] ?? '1' }}" type="text" placeholder="{{ __('Add Quantity') }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-2 col-sm-6">
                                                                        <button class="@if($key == 0) d-none @endif btn btn-danger remove-service mt-3"><i class="las la-times"></i></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div wire:ignore class="btn-wrapper mt-3">
                                                        <a href="javascript:void(0)" class="btn-see-more style-02 color-3 hide_service_and_show">{{ __('Add More') }}</a>
                                                    </div>
                                                </div>
                                                <!-- include markup start -->
                                            </div>
                                            <!-- Include Service end -->


                                                <!-- Online Service start -->
                                            <div wire:ignore class="single-settings day_review_show_hide">
                                                <div class="single-dashboard-input mt-4">
                                                    <div class="row g-4">
                                                        <div class="col-lg-4 col-sm-6">
                                                            <div class="single-info-input">
                                                                <label class="label_title">{{ __('Delivery Days') }} <span class="text-danger">*</span></label>
                                                                <input class="form--control" type="number" id="service_online_delivery_days" name="delivery_days" placeholder="{{ __('Delivery Days') }}" value="{{ $services->delivery_days ?? '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-sm-6">
                                                            <div class="single-info-input">
                                                                <label class="label_title">{{ __('Revisions') }}</label>
                                                                <input class="form--control" type="number" id="service_online_revisions" name="revisions" placeholder="{{ __('Revisions') }}" value="{{ $services->revision ?? '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-sm-6">
                                                            <div class="single-settings">
                                                                <div class="single-dashboard-input">
                                                                    <div class="single-info-input">
                                                                        <label class="label_title">{{ __('Service Price') }} <span class="text-danger">*</span></label>
                                                                        <input class="form--control" type="number" id="service_online_price" name="price" placeholder="{{ __('Service Price') }}" value="{{ $services->price ?? '' }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Online Service end -->
                                        </div>

                                        <!-- Additional Service Start -->
                                        <div class="tab-pane fade" id="additional-service" role="tabpanel" aria-labelledby="additional-service-tab">
                                            <div class="single-settings mt-4">
                                                <h4 class="input-title">{{__('Add Additional Services')}}</h4>
                                                <div class="append-additional-services mt-4">
                                                    <div class="single-dashboard-input additional-services">
                                                        @foreach($additional_service_inputs as $key_additional_id => $value)
                                                            <div class="additional-service-field" data-index="{{ $key_additional_id }}">
                                                                <div class="row g-4 mt-5">
                                                                    <div class="col-xl-3 col-sm-6">
                                                                        <div class="single-info-input">
                                                                            <label class="label_title">{{ __('Title') }} <span class="text-danger">*</span></label>
                                                                            <input class="form--control" type="text" name="additional_service_inputs[{{ $key_additional_id }}][additional_service_title]" placeholder="{{__('Service title')}}" value="{{ $value['additional_service_title'] ?? '' }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xl-2 col-sm-6">
                                                                        <div class="single-info-input">
                                                                            <label class="label_title">{{ __('Unit Price') }} <span class="text-danger">*</span></label>
                                                                            <input class="form--control numeric-value" type="number" name="additional_service_inputs[{{ $key_additional_id }}][additional_service_price]" step="0.01" placeholder="{{__('Add Price')}}" value="{{ $value['additional_service_price'] ?? '' }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xl-2 col-sm-6">
                                                                        <div class="single-info-input">
                                                                            <label class="label_title">{{ __('Quantity') }}</label>
                                                                            <input class="form--control numeric-value" type="text" name="additional_service_inputs[{{ $key_additional_id }}][additional_service_quantity]" value="{{ $value['additional_service_quantity'] ?? '1' }}" placeholder="{{__('Add Quantity')}}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xl-3 col-sm-6">
                                                                        <div class="single-info-input">
                                                                            <div class="form-group">
                                                                                <div class="media-upload-btn-wrapper">
                                                                                    <div class="img-wrap">
                                                                                        <div class="img-wrap-new">
                                                                                            {!! render_image_markup_by_attachment_id($value['additional_service_image'] ?? '') !!}
                                                                                        </div>
                                                                                    </div>
                                                                                    <input type="hidden" name="additional_service_inputs[{{ $key_additional_id }}][additional_service_image]" class="additional_service_image" value="{{ $value['additional_service_image'] ?? '' }}">
                                                                                    <button data-value="{{ $key_additional_id }}" type="button" class="new_set_additional_service_image btn btn-info media_upload_form_btn" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}" data-bs-toggle="modal" data-bs-target="#media_upload_modal">
                                                                                        {{__('Upload Image')}}
                                                                                    </button>
                                                                                    <small style="font-size: 10px">{{ __('image format: jpg,jpeg,png')}} ({{ __('recommended size 78x78') }})</small>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xl-2 col-sm-2">
                                                                        <span class="@if($key_additional_id == 0) d-none @endif btn btn-danger additional-remove"><i class="las la-times"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="btn-wrapper mt-3">
                                                    <a href="javascript:void(0)" class="btn-see-more style-02 color-3 add-additional-services">{{ __('Add More') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Additional Service end -->

                                        <!-- Service Benefit Start -->
                                        <div class="tab-pane fade" id="benefit-service" role="tabpanel" aria-labelledby="benefit-service-tab">
                                            <div class="single-settings margin-top-40">
                                                <h4 class="input-title">{{__('Benefit Of This Package')}}</h4>
                                                <div class="append-benifits">
                                                    @foreach($service_benefit_inputs as $key_benefit_id => $value)
                                                        <div class="single-dashboard-input" data-index="{{ $key_benefit_id }}">
                                                            <div class="row align-items-center g-4">
                                                                <div class="col-xl-10 col-sm-9">
                                                                    <div class="single-info-input mt-3">
                                                                        <input class="form--control" type="text" name="service_benefit_inputs[{{ $key_benefit_id }}][benifits]" placeholder="{{__('Type Here')}}" value="{{ $value['benifits'] ?? '' }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-xl-2 col-sm-3">
                                                                    <span class="@if($key_benefit_id == 0) d-none @endif btn btn-danger benefit-remove"><i class="las la-times"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="btn-wrapper mt-3">
                                                    <a href="javascript:void(0)" class="btn-see-more style-02 color-3 service-benefit-add">{{ __('Add More') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Service Benefit end -->

                                        <!-- Service Faqs Start -->
                                        <div class="tab-pane fade" id="faq-service" role="tabpanel" aria-labelledby="faq-service-tab">
                                            <div class="single-settings margin-top-40 faq_show_hide">
                                                <h4 class="input-title">{{__('Faqs')}}</h4>
                                                <div class="append-faqs">
                                                    @foreach($online_service_faq as $key_faq_id => $value)
                                                        <div class="row" data-index="{{ $key_faq_id }}">
                                                            <div class="col-xl-10">
                                                                <div class="single-dashboard-input faqs">
                                                                    <div class="single-info-input mt-3">
                                                                        <label class="label_title">{{__('Title')}}</label>
                                                                        <input class="form--control" type="text" name="online_service_faq[{{ $key_faq_id }}][title]" placeholder="{{__('Faq Title')}}" value="{{ $value['title'] ?? '' }}">
                                                                    </div>
                                                                    <div class="single-info-input mt-3">
                                                                        <label class="label_title">{{__('Description')}}</label>
                                                                        <textarea class="form--control textarea-input" name="online_service_faq[{{ $key_faq_id }}][description]" cols="20" rows="2" placeholder="{{__('Faq Description')}}" style="padding-top: 16px;">{{ $value['description'] ?? '' }}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xl-2">
                                                                <span class="btn btn-danger remove-faqs mt-3 @if($key_faq_id == 0) d-none @endif"><i class="las la-times"></i></span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="btn-wrapper mt-3">
                                                    <a href="javascript:void(0)" class="btn-see-more style-02 color-3 service-faqs-add">{{ __('Add More') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Service Faqs end -->
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- service attribute end-->

                <!-- service Meta Section start-->
                <div  wire:ignore  class="tab-pane fade @if($current_tab === "services-meta-tab") show active @endif step" id="services-meta" role="tabpanel" aria-labelledby="services-meta-tab">
                    <div class="card">
                        <div class="card-body meta">
                            <h5 class="header-title">{{__('Meta Section')}}</h5>
                            <div class="row g-4 mt-1">
                                <div class="col-xxl-2 col-xl-3 col-sm-4">
                                    <div class="nav nav-pills flex-column" id="v-pills-tab"
                                         role="tablist" aria-orientation="vertical">
                                        <a class="nav-link active" id="v-pills-home-tab"
                                           data-bs-toggle="pill" href="#v-pills-home" role="tab"
                                           aria-controls="v-pills-home"
                                           aria-selected="true">
                                            <span class="nav-link-number">{{__('1')}}</span>
                                            {{__('Service Meta')}}</a>
                                        <a class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill"
                                           href="#v-pills-profile" role="tab"
                                           aria-controls="v-pills-profile"
                                           aria-selected="false">
                                            <span class="nav-link-number">{{__('2')}}</span>
                                            {{__('Facebook Meta')}}</a>
                                        <a class="nav-link" id="v-pills-messages-tab" data-bs-toggle="pill"
                                           href="#v-pills-messages" role="tab"
                                           aria-controls="v-pills-messages"
                                           aria-selected="false">
                                            <span class="nav-link-number">{{__('3')}}</span>
                                            {{__('Twitter Meta')}}</a>
                                    </div>
                                </div>
                                <div class="col-xxl-10 col-xl-9 col-sm-8">
                                    <div class="tab-content meta-content" id="v-pills-tabContent">
                                        <!-- General Meta -->
                                        <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                                            <div class="row g-4">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label class="label_title" for="meta_title">{{__('Meta Title')}}</label>
                                                        <input type="text" class="form-control" id="meta_title" name="meta_title" placeholder="{{__('Title')}}" value="{{ $meta['meta_title'] ?? '' }}" wire:model.defer="meta.meta_title">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label class="label_title" for="meta_tags">{{__('Meta Tags')}}</label>
                                                        <input type="text" class="form-control" name="meta_tags" placeholder="Slug" data-role="tagsinput" id="meta_tags" value="{{ $meta['meta_tags'] ?? '' }}" wire:model.defer="meta.meta_tags">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label class="label_title" for="meta_description">{{__('Meta Description')}}</label>
                                                        <textarea name="meta_description" class="form-control textarea-input" cols="20" rows="2" placeholder="{{ __('Description') }}" wire:model.defer="meta.meta_description">{{ $meta['meta_description'] ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Facebook Meta -->
                                        <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                            <div class="row g-4">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label class="label_title" for="facebook_meta_tags">{{__('Facebook Meta Title')}}</label>
                                                        <input type="text" class="form-control" name="facebook_meta_tags" placeholder="{{__('Title')}}" value="{{ $meta['facebook_meta_tags'] ?? '' }}" wire:model.defer="meta.facebook_meta_tags">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label class="label_title" for="facebook_meta_description">{{__('Facebook Meta Description')}}</label>
                                                        <textarea name="facebook_meta_description" class="form-control textarea-input" cols="20" rows="2" placeholder="{{ __('Description') }}" wire:model.defer="meta.facebook_meta_description">{{ $meta['facebook_meta_description'] ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label class="label_title" for="facebook_meta_image">{{__('Facebook Meta Image')}}</label>
                                                        <div class="media-upload-btn-wrapper">
                                                            <div class="img-wrap">
                                                                {!! render_attachment_preview_for_admin($meta['facebook_meta_image'] ?? '') !!}
                                                            </div>
                                                            <input type="hidden" name="facebook_meta_image" id="facebook_meta_image" value="{{ $meta['facebook_meta_image'] ?? '' }}" wire:model.defer="meta.facebook_meta_image">
                                                            <button type="button" class="change_service_facebook_image btn btn-info media_upload_form_btn" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}" data-bs-toggle="modal" data-bs-target="#media_upload_modal">
                                                                {{__('Upload Image')}}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Twitter Meta -->
                                        <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                            <div class="row g-4">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label class="label_title" for="twitter_meta_tags">{{__('Twitter Meta Title')}}</label>
                                                        <input type="text" class="form-control" name="twitter_meta_tags" placeholder="{{__('Title')}}" value="{{ $meta['twitter_meta_tags'] ?? '' }}" wire:model.defer="meta.twitter_meta_tags">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label class="label_title" for="twitter_meta_description">{{__('Twitter Meta Description')}}</label>
                                                        <textarea name="twitter_meta_description" class="form-control textarea-input" cols="20" rows="2" placeholder="{{ __('Description') }}" wire:model.defer="meta.twitter_meta_description">{{ $meta['twitter_meta_description'] ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label class="label_title" for="twitter_meta_image">{{__('Twitter Meta Image')}}</label>
                                                        <div class="media-upload-btn-wrapper">
                                                            <div class="img-wrap">
                                                                {!! render_attachment_preview_for_admin($meta['twitter_meta_image'] ?? '') !!}
                                                            </div>
                                                            <input type="hidden" name="twitter_meta_image" id="twitter_meta_image" value="{{ $meta['twitter_meta_image'] ?? '' }}" wire:model.defer="meta.twitter_meta_image">
                                                            <button type="button" class="change_service_twitter_image btn btn-info media_upload_form_btn" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}" data-bs-toggle="modal" data-bs-target="#media_upload_modal">
                                                                {{__('Upload Image')}}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- service Meta Section end-->

                <!-- service Media Uploads start-->
                <div class="tab-pane fade @if($current_tab === "service-media-uploads-tab") show active @endif step" id="service-media-uploads" role="tabpanel" aria-labelledby="service-media-uploads-tab">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="single-dashboard-input">
                                <div class="single-dashboard-input">
                                    <div class="single-info-input">
                                        <div class="form-group ">
                                            <div class="media-upload-btn-wrapper">
                                                <div class="img-wrap">
                                                    {!! render_image_markup_by_attachment_id($services['image'],'','thumb') !!}
                                                </div>
                                                <input type="hidden" id="service_image" name="image" value="{{ $services['image'] ?? '' }}">
                                                <button type="button" class="change_service_image btn btn-info media_upload_form_btn"
                                                        data-btntitle="{{__('Select Image')}}"
                                                        data-modaltitle="{{__('Upload Image')}}" data-bs-toggle="modal"
                                                        data-bs-target="#media_upload_modal">
                                                    {{__('Upload Main Image')}}
                                                </button>
                                                <small>{{ __('image format: jpg,jpeg,png')}}</small> <br>
                                                <small>{{ __('recommended size 1920x1280') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <div class="media-upload-btn-wrapper">
                                        <div class="img-wrap">
                                            {!! render_gallery_image_attachment_preview($services['image_gallery'] ?? '') !!}
                                        </div>
                                        <input type="hidden" name="image_gallery" id="image_gallery" value="{{ $services['image_gallery'] ?? '' }}">
                                        <button type="button" class="change_service_gallery_image btn btn-info media_upload_form_btn"
                                                data-btntitle="{{__('Select Image')}}"
                                                data-modaltitle="{{__('Upload Image')}}"
                                                data-bs-toggle="modal"
                                                data-mulitple="true"
                                                data-bs-target="#media_upload_modal">
                                            {{__('Upload Gallery Image')}}
                                        </button>
                                        <small>{{ __('image format: jpg,jpeg,png')}}</small> <br>
                                        <small>{{ __('recommended size 1920x1280') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- service Media Uploads end-->

                <!-- service Set Availability start-->
                <div   wire:ignore class="tab-pane fade @if($current_tab === "service-set-availability-tab") show active @endif step" id="service-set-availability" role="tabpanel" aria-labelledby="service-set-availability-tab">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="available-all-city-area">
                                        <span class="text-info">{{__('Is Available All Cities and Area')}}</span>
                                        <div class="dashboard-switch-single d-flex">
                                            <input type="hidden" name="is_service_all_cities" value="0">
                                            <input class="custom-switch is_service_all_cities_id" id="is_service_all_cities"
                                                   type="checkbox" name="is_service_all_cities"
                                                   value="1" @if($services['is_service_all_cities'] == 1) checked @endif />
                                            <label class="switch-label mt-2" for="is_service_all_cities"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- service Set Availability end-->
            </div>
        </div>
    </div>


    <!-- start previous / next buttons -->
    <div  wire:ignore class="col-lg-12">
        <div class="single-settings">
            <div class="btn-wrapper d-flex gap-2 mt-4">
                <button class="dashboard_table__title__btn btn-outline-1 radius-5" id="prevBtn" type="button">{{__('Previous')}}</button>
                <button class="dashboard_table__title__btn btn-bg-1 radius-5" style="border: none" id="nextBtn" type="button">{{__('Next')}}</button>

                <button class="dashboard_table__title__btn btn-bg-1 radius-5"
                        style="border: none; @if($hideSubmitButton) display: none @endif"
                        id="submitBtn" type="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="btn-disabled"> {{__('Update Service')}}
                </button>
            </div>
        </div>
    </div>
</form>

<!-- for select -->
@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize include index based on existing fields
            let includeIndex = {{ count($include_service_inputs) }};
            let additionalIndex = {{ count($additional_service_inputs) }};
            let benefitIndex = {{ count($service_benefit_inputs) }};
            let faqIndex = {{ count($online_service_faq) }};

            // Set initial is_service_online state
            function toggleOnlineServiceFields() {
                let isServiceOnline = $('#is_service_online').is(':checked');

                // Update visibility for all existing service input fields
                $('.include-service-field').each(function () {
                    const $priceField = $(this).find('.is_service_online_hide').eq(0); // Unit Price container
                    const $quantityField = $(this).find('.is_service_online_hide').eq(1); // Quantity container

                    if (isServiceOnline) {
                        $priceField.addClass('d-none');
                        $quantityField.addClass('d-none');
                    } else {
                        $priceField.removeClass('d-none');
                        $quantityField.removeClass('d-none');
                    }
                });

                // Toggle other elements
                if (isServiceOnline) {
                    $('.day_review_show_hide').show(); // Show delivery days, revisions, price
                    $('.faq_show_hide').show(); // Show FAQ tab
                    $('.service-price-show-hide').hide(); // Hide total service price
                } else {
                    $('.day_review_show_hide').hide(); // Hide delivery days, revisions, price
                    $('.faq_show_hide').hide(); // Hide FAQ tab
                    $('.service-price-show-hide').show(); // Show total service price
                    let total = 0;
                    $(".include-price").each(function () {
                        const val = parseFloat($(this).val());
                        if (!isNaN(val)) total += val;
                    });
                    $("#service_total_price").val(total);
                }
            }

            // Set initial state based on PHP data and call toggle function
            let online_check = '{{ $services->is_service_online ?? 0 }}';
            if (online_check === '1') {
                $('#is_service_online').prop('checked', true);
            } else {
                $('#is_service_online').prop('checked', false);
            }
            toggleOnlineServiceFields(); // Apply initial visibility

            // Handle is_service_online checkbox change
            $("#is_service_online").on('change', function () {
                toggleOnlineServiceFields(); // Toggle visibility on change
            });
            // Handle "Add More" click
            $('.hide_service_and_show').on('click', function () {
                includeIndex++;

                // Check if service is online
                let isServiceOnline = $('#is_service_online').is(':checked');
                let priceFieldClass = isServiceOnline ? 'd-none is_service_online_hide' : 'is_service_online_hide';
                let quantityFieldClass = isServiceOnline ? 'd-none is_service_online_hide' : 'is_service_online_hide';

                // Create new input field HTML
                let newField = `
                    <div wire:ignore.self class="single-dashboard-input what-include-element mt-4 include-service-field" data-index="${includeIndex}">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-4 col-sm-6">
                                <div class="single-info-input">
                                    <label class="label_title">{{__('Title')}} <span class="text-danger">*</span></label>
                                    <input class="form--control" type="text" name="include_service_inputs[${includeIndex}][include_service_title]" placeholder="{{__('Service Title')}}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 ${priceFieldClass}">
                                <div class="single-info-input">
                                    <label class="label_title">{{__('Unit Price')}} <span class="text-danger">*</span></label>
                                    <input class="form--control include-price" type="text" name="include_service_inputs[${includeIndex}][include_service_price]" placeholder="{{__('Add Price')}}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 ${quantityFieldClass}">
                                <div class="single-info-input">
                                    <label class="label_title">{{__('Quantity')}}</label>
                                    <input class="form--control numeric-value" type="text" name="include_service_inputs[${includeIndex}][include_service_quantity]" value="1" placeholder="{{__('Add Quantity')}}">
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6">
                                <button type="button" class="btn btn-danger remove-service mt-3"><i class="las la-times"></i></button>
                            </div>
                        </div>
                    </div>
                `;

                // Append to container
                $('.include-services-container').append(newField);

                // Sync with Livewire
            @this.set('include_service_inputs.' + includeIndex, {
                include_service_title: '',
                include_service_price: ''
            }, true);
            });

            // Handle "Remove" button click
            $(document).on('click', '.remove-service', function () {
                let index = $(this).closest('.include-service-field').data('index');
                $(this).closest('.include-service-field').remove();

                // Recalculate total price after removal
                var sum = 0;
                $(".include-price").each(function () {
                    if (!isNaN($(this).val()) && $(this).val() !== '') {
                        sum += parseFloat($(this).val());
                    }
                });
                $("#service_total_price").val(sum);

                // Remove from Livewire state
                let inputs = @this.get('include_service_inputs');
                if (inputs[index]) {
                    delete inputs[index];
                @this.set('include_service_inputs', inputs, true);
                }
            });

            // Handle "Add More" click for Additional Services
            $('.add-additional-services').off('click').on('click', function () {

                additionalIndex++;
                let newField = `
            <div class="additional-service-field" data-index="${additionalIndex}">
                <div class="row g-4 mt-5">
                    <div class="col-xl-3 col-sm-6">
                        <div class="single-info-input">
                            <label class="label_title">{{__('Title')}} <span class="text-danger">*</span></label>
                            <input class="form--control" type="text" name="additional_service_inputs[${additionalIndex}][additional_service_title]" placeholder="{{__('Service Title')}}">
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-6">
                        <div class="single-info-input">
                            <label class="label_title">{{__('Unit Price')}} <span class="text-danger">*</span></label>
                            <input class="form--control numeric-value" type="number" name="additional_service_inputs[${additionalIndex}][additional_service_price]" step="0.01" placeholder="{{__('Add Price')}}">
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-6">
                        <div class="single-info-input">
                            <label class="label_title">{{__('Quantity')}}</label>
                            <input class="form--control numeric-value" type="text" name="additional_service_inputs[${additionalIndex}][additional_service_quantity]" value="1" placeholder="{{__('Add Quantity')}}">
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="single-info-input">
                            <div class="form-group">
                                <div class="media-upload-btn-wrapper">
                                    <div class="img-wrap">
                                        <div class="img-wrap-new"></div>
                                    </div>
                                    <input type="hidden" name="additional_service_inputs[${additionalIndex}][additional_service_image]" class="additional_service_image">
                                    <button data-value="${additionalIndex}" type="button" class="new_set_additional_service_image btn btn-info media_upload_form_btn" data-btntitle="{{__('Select Image')}}" data-modaltitle="{{__('Upload Image')}}" data-bs-toggle="modal" data-bs-target="#media_upload_modal">
                                        {{__('Upload Image')}}
                                    </button>
                                    <small style="font-size: 10px">image format: jpg,jpeg,png ({{__('recommended size')}} 78x78)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-2">
                        <span class="btn btn-danger additional-remove"><i class="las la-times"></i></span>
                    </div>
                </div>
            </div>
        `;

                $('.append-additional-services').append(newField);
            });

            // Handle "Remove" click for Additional Services
            $(document).off('click', '.additional-remove').on('click', '.additional-remove', function () {
                $(this).closest('.additional-service-field').remove();
            });

            // Handle "Add More" click for Service Benefits
            $('.service-benefit-add').off('click').on('click', function () {
                benefitIndex++;
                let newField = `
            <div class="single-dashboard-input" data-index="${benefitIndex}">
                <div class="row align-items-center g-4">
                    <div class="col-xl-10 col-sm-9">
                        <div class="single-info-input mt-3">
                            <input class="form--control" type="text" name="service_benefit_inputs[${benefitIndex}][benifits]" placeholder="{{__('Type Here')}}">
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-3">
                        <span class="${benefitIndex == 0 ? 'd-none' : ''} btn btn-danger benefit-remove"><i class="las la-times"></i></span>
                    </div>
                </div>
            </div>
        `;

                $('.append-benifits').append(newField);
            });

            // Handle "Remove" click for Service Benefits
            $(document).off('click', '.benefit-remove').on('click', '.benefit-remove', function () {
                $(this).closest('.single-dashboard-input').remove();
            });

            // Handle "Add More" click for Service FAQs
            $('.service-faqs-add').off('click').on('click', function () {
                faqIndex++;
                let newField = `
            <div class="row" data-index="${faqIndex}">
                <div class="col-xl-10">
                    <div class="single-dashboard-input faqs">
                        <div class="single-info-input mt-3">
                            <label class="label_title">{{__('Title')}}</label>
                            <input class="form--control" type="text" name="online_service_faq[${faqIndex}][title]" placeholder="{{__('Faq Title')}}">
                        </div>
                        <div class="single-info-input mt-3">
                            <label class="label_title">{{__('Description')}}</label>
                            <textarea class="form--control textarea-input" name="online_service_faq[${faqIndex}][description]" cols="20" rows="2" placeholder="{{__('Faq Description')}}" style="padding-top: 16px;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2">
                    <span class="btn btn-danger remove-faqs mt-3 ${faqIndex == 0 ? 'd-none' : ''}"><i class="las la-times"></i></span>
                </div>
            </div>
        `;

                $('.append-faqs').append(newField);
            });

            // Handle "Remove" click for Service FAQs
            $(document).off('click', '.remove-faqs').on('click', '.remove-faqs', function () {
                $(this).closest('.row').remove();
            });

            // Sync category, subcategory, child category
            let class_name_for_category = '.is_service_online, .hide_service_and_show, .remove-service';
            $(document).on('click', class_name_for_category, function () {
                let cat_get_value = $("#category").val();
                let sub_cat_get_value = $("#subcategory").val();
                let child_cat_get_value = $("#child_category").val();
            @this.set('category', cat_get_value, true);
            @this.set('subcategory', sub_cat_get_value, true);
            @this.set('child_category', child_cat_get_value, true);
            });



            // Update tab value
            $('#serviceTab .nav-link').on('click', function () {
                $('#current_tab').val($(this).attr('id'));
            });

            // Initialize Jodit editor
            let jodit = null;
            if ($('#jodit-editor').length && !$('#jodit-editor').hasClass('jodit-initialized')) {
                $('#jodit-editor').addClass('jodit-initialized');
                jodit = Jodit.make('#jodit-editor', {
                    height: 200,
                    placeholder: '{{ __("Type Description") }}',
                    buttons: [
                        'bold', 'italic', 'underline', '|',
                        'ul', 'ol', '|',
                        'outdent', 'indent', '|',
                        'font', 'fontsize', 'brush', 'paragraph', '|',
                        'align', 'undo', 'redo', '|',
                        'link', 'image', 'video', 'table', '|',
                        'hr', 'eraser', 'fullsize', '|',
                    ],
                    uploader: {
                        insertImageAsBase64URI: true
                    }
                });

                // Sync Jodit content with hidden textarea
                jodit.events.on('change', () => {
                    $('#description').val(jodit.getEditorValue());
                });

                // Set initial content if exists (for edit forms)
                const initialContent = $('#description').val();
                if (initialContent && initialContent.trim() !== '') {
                    jodit.setEditorValue(initialContent);
                }
            }
        });
    </script>
@endpush