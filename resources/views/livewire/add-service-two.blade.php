<form enctype="multipart/form-data" action="{{ route('seller.add.services') }}" method="POST" id="service-form">
    @csrf
     <div class="mt-4">
         @if (session()->has('message'))
             <div class="alert alert-danger">
                 {{ session('message') }}
             </div>
         @endif
     </div>

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
                <a class="nav-link @if($current_tab === "service-attribute-tab") active @endif stepIndicator" id="service-attribute-tab" data-bs-toggle="pill"
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

                    <!-- service Info start-->
                    <div  wire:ignore class="tab-pane fade @if($current_tab === "service-info-tab") show active @endif step" id="service-info" role="tabpanel" aria-labelledby="service-info-tab">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="single-dashboard-input">
                                    <div class="row g-4">
                                        <div class="col-sm-6">
                                            <div class="single-info-input">
                                                <label for="title" class="info-title"> {{__('Service Title')}} <span class="text-danger">*</span> </label>
                                                <input class="form--control" name="title" id="title" type="text" placeholder="{{ __('Add title')}}" wire:model.defer="services.title">
                                            </div>
                                            <div class="single-dashboard-input mt-3">
                                                <div class="single-info-input permalink_label">
                                                    <label for="title" class="info-title text-dark"> {{__('Permalink')}} <span class="text-danger">*</span> </label>
                                                    <span id="slug_show" class="display-inline"></span>
                                                    <span id="slug_edit" class="display-inline"> </span>
                                                    <button class="btn btn-warning btn-sm slug_edit_button">  <i class="las la-edit"></i> </button>
                                                    <input class="form--control service_slug" name="slug" id="slug" style="display: none" type="text" wire:model.defer="services.slug">
                                                    <button class="btn btn-info btn-sm slug_update_button mt-2" style="display: none">{{  __('Update')}}</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="single-info-input">
                                                <label for="video" class="info-title"> {{__('Service Video Url')}} </label>
                                                <input class="form--control" name="video" id="video" type="text" placeholder="{{__('youtube embed code')}}" wire:model.defer="services.video">
                                                <small class="text-danger">{{__('must be embed code from youtube.')}} <span class="text-dark">{{ __('Ex. <iframe width="560" height="315" src="https://www.youtube.com"></iframe>')  }}</span> </small>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="single-info-input">
                                                <label for="description" class="info-title">{{ __('Service Description') }} <span class="text-danger">*</span> <small class="text-info">{{ __('minimum 150 characters') }}</small></label>
                                                <!-- Jodit Editor Container -->
                                                <textarea id="jodit-editor" style="height: 200px;"></textarea>
                                                <!-- Hidden textarea for form submission -->
                                                <textarea name="description" id="description" class="form--control d-none"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- service Info end-->

                    <!-- service Category start-->
                    <div wire:ignore  class="tab-pane fade @if($current_tab === "service-category-tab") show active @endif step" id="service-category" role="tabpanel" aria-labelledby="service-category-tab">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="single-dashboard-input">
                                    <div class="row g-4">
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="single-info-input">
                                                <label for="category" class="info-title"> {{__('Select Main Category')}} <span class="text-danger">*</span> </label>
                                                <select class="select2" name="category" id="category" wire:model.defer="category">
                                                    <option value="">{{__('Select Category')}}</option>
                                                    @foreach($categories as $cat)
                                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="single-info-input sub_category_wrapper">
                                                <label for="subcategory" class="info-title"> {{__('Select Sub Category')}} </label>
                                                <select  name="subcategory" id="subcategory" class="subcategory" wire:model.defer="subcategory"></select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="single-info-input child_category_wrapper">
                                                <label for="child_category" class="info-title"> {{__('Select Child Category')}} </label>
                                                <select  name="child_category" id="child_category" wire:model.defer="child_category"></select>
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
                                    <!-- include service tabs markup start -->
                                    <div class="nav nav-pills flex-column" id="add-service-attribute-tab"
                                         role="tablist" aria-orientation="vertical">
                                        <a class="nav-link active" id="included-service-tab"
                                           data-bs-toggle="pill" href="#included-service" role="tab"
                                           aria-controls="included-service"
                                           aria-selected="true">
                                            <span class="nav-link-number">{{__('1')}}</span>
                                            {{__('Included Service')}}
                                        </a>
                                        <a class="nav-link" id="additional-service-tab" data-bs-toggle="pill"
                                           href="#additional-service" role="tab"
                                           aria-controls="additional-service"
                                           aria-selected="false">
                                            <span class="nav-link-number">{{__('2')}}</span>
                                            {{__('Additional Service')}}
                                        </a>
                                        <a class="nav-link" id="benefit-service-tab" data-bs-toggle="pill"
                                           href="#benefit-service" role="tab"
                                           aria-controls="benefit-service"
                                           aria-selected="false">
                                            <span class="nav-link-number">{{__('3')}}</span>
                                            {{__('Benefit')}}
                                        </a>
                                        <a class="nav-link faq_show_hide" id="faq-service-tab" data-bs-toggle="pill"
                                           href="#faq-service" role="tab"
                                           aria-controls="faq-service"
                                           aria-selected="false">
                                            <span class="nav-link-number">{{__('4')}}</span>
                                            {{__('Faq')}}
                                        </a>
                                    </div>
                                     <!-- include service tabs markup end -->

                                     <!--service price show start -->
                                    <div class="edit-service-wrappers mt-4">
                                        <div class="single-dashboard-input service-price-show-hide">
                                            <div class="single-info-input">
                                                <label class="info-title"> {{__('Service Price')}}</label>
                                                <input class="form--control" type="text" name="price" id="service_total_price" disabled>
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

                                            <div wire:ignore.self class="tab-pane fade active show" id="included-service" role="tabpanel" aria-labelledby="included-service-tab">
                                                <!-- Include Service start -->
                                                <div class="single-settings">
                                                    <div class="dashboard_table__title__flex">
                                                        <div class="dashboard__headerContents__left">
                                                            <h4 class="input-title">{{ __('What\'s Included This Package') }}</h4>
                                                            <div class="online_service mt-3">
                                                                <div class="dashboard-switch-single d-flex gap-1">
                                                                    <span class="text-info">{{ __('Is Service Online') }}</span>
                                                                    <input class="custom-switch is_service_online" id="is_service_online" type="checkbox" name="is_service_online">
                                                                    <label class="switch-label mt-0" for="is_service_online"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Include markup start -->
                                                    <div class="add-input append-additional-includes mt-4">
                                                        <!-- Initial input field (always visible) -->
                                                        <div class="single-dashboard-input what-include-element">
                                                            <div class="row g-4">
                                                                <div class="col-lg-4 col-sm-6">
                                                                    <div class="single-info-input">
                                                                        <label class="label_title">{{ __('Title') }} <span class="text-danger">*</span></label>
                                                                        <input class="form--control" type="text" name="include_service_title[]" placeholder="{{ __('Service Title') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 col-sm-6 is_service_online_hide">
                                                                    <div class="single-info-input">
                                                                        <label class="label_title">{{ __('Unit Price') }} <span class="text-danger">*</span></label>
                                                                        <input class="form--control include-price" type="number" name="include_service_price[]" placeholder="{{ __('Add Price') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 col-sm-6 is_service_online_hide">
                                                                    <div class="single-info-input">
                                                                        <label class="label_title">{{ __('Quantity') }}</label>
                                                                        <input class="form--control numeric-value" type="text" value="1" placeholder="{{ __('Add Quantity') }}" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Container for dynamically added include service fields -->
                                                        <div class="include-services-container"></div>

                                                        <!-- Add More button -->
                                                        <div class="btn-wrapper mt-3">
                                                            <a href="javascript:void(0)" class="btn-see-more style-02 color-3 add-more-service">{{ __('Add More') }}</a>
                                                        </div>
                                                    </div>
                                                    <!-- Include markup end -->
                                                </div>
                                                <!-- Include Service end -->

                                                <!-- Online Service start -->
                                                <div  wire:ignore class="single-settings day_review_show_hide">
                                                    <div class="single-dashboard-input mt-4">
                                                        <div class="row g-4">
                                                            <div class="col-lg-4 col-sm-6">
                                                                <div class="single-info-input">
                                                                    <label class="label_title">{{ __('Delivery Days') }} <span class="text-danger">*</span> </label>
                                                                    <input class="form--control" type="number"  wire:model.defer="online_service.delivery_days" name="delivery_days" placeholder="{{__('Delivery Days')}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col-sm-6">
                                                                <div class="single-info-input">
                                                                    <label class="label_title">{{ __('Revisions') }}</label>
                                                                    <input class="form--control" type="number"  wire:model.defer="online_service.revision" name="revision" placeholder="{{__('Revision Times')}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col-sm-6">
                                                                <div class="single-settings online_service_price_show_hide">
                                                                    <div class="single-dashboard-input">
                                                                        <div class="single-info-input">
                                                                            <label class="label_title">{{ __('Service Price') }} <span class="text-danger">*</span> </label>
                                                                            <input class="form--control" type="number" step="0.01" wire:model.defer="online_service.online_service_price" name="online_service_price" placeholder="{{__('Service price')}}">
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
                                                    <h4 class="input-title">{{ __('Add Additional Services') }}</h4>

                                                    <div class="append-additional-services mt-4">
                                                        <div class="single-dashboard-input additional-services">
                                                            <!-- Default additional service markup start -->
                                                            <div class="row g-4 mt-2 additional-service-element">
                                                                <div class="col-xl-3 col-sm-6">
                                                                    <div class="single-info-input">
                                                                        <label class="label_title">{{ __('Title') }} <span class="text-danger">*</span></label>
                                                                        <input class="form--control" type="text" name="additional_service_title[]" placeholder="{{ __('Service title') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-xl-2 col-sm-6">
                                                                    <div class="single-info-input">
                                                                        <label class="label_title">{{ __('Unit Price') }} <span class="text-danger">*</span></label>
                                                                        <input class="form--control numeric-value" type="number" name="additional_service_price[]" placeholder="{{ __('Add Price') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-xl-2 col-sm-6">
                                                                    <div class="single-info-input">
                                                                        <label class="label_title">{{ __('Quantity') }}</label>
                                                                        <input class="form--control numeric-value" type="text" value="1" placeholder="{{ __('Add Quantity') }}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-xl-3 col-sm-6">
                                                                    <div class="single-info-input">
                                                                        <div class="form-group">
                                                                            <div class="media-upload-btn-wrapper">
                                                                                <div class="img-wrap mb-0"></div>
                                                                                <input type="hidden" name="additional_service_image[0]" class="additional_service_image">
                                                                                <button data-value="0" type="button" class="btn btn-info media_upload_form_btn"
                                                                                        data-btntitle="{{ __('Select Image') }}"
                                                                                        data-modaltitle="{{ __('Upload Image') }}" data-bs-toggle="modal"
                                                                                        data-bs-target="#media_upload_modal">
                                                                                    {{ __('Upload Image') }}
                                                                                </button>
                                                                                <small style="font-size: 10px">{{ __('image format: jpg,jpeg,png') }} <span> ({{ __('recommended size 78x78') }}) </span></small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Default additional service markup end -->

                                                            <!-- Container for dynamically added additional service fields -->
                                                            <div class="additional-services-container"></div>
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
                                                    <h4 class="input-title">{{ __('Benefit Of This Package') }}</h4>
                                                    <div class="append-benifits">
                                                        <div class="single-dashboard-input benifits">
                                                            <!-- Default benefit markup start -->
                                                            <div class="single-info-input mt-3">
                                                                <input class="form--control" type="text" name="benifits[]" placeholder="{{ __('Type Here') }}">
                                                            </div>
                                                            <!-- Default benefit markup end -->

                                                            <!-- Container for dynamically added benefit fields -->
                                                            <div class="benifits-container"></div>
                                                        </div>
                                                    </div>
                                                    <div class="btn-wrapper mt-3">
                                                        <a href="javascript:void(0)" class="btn-see-more style-02 color-3 add-benefit">{{ __('Add More') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Service Benefit End -->

                                            <!-- Service Faqs Start -->
                                            <div class="tab-pane fade" id="faq-service" role="tabpanel" aria-labelledby="faq-service-tab">
                                                <div class="single-settings margin-top-40 faq_show_hide">
                                                    <h4 class="input-title">{{ __('Faqs') }}</h4>
                                                    <div class="append-faqs">
                                                        <div class="single-dashboard-input faqs">
                                                            <!-- Default FAQ markup start -->
                                                            <div class="single-info-input mt-3">
                                                                <label class="label_title">{{ __('Title') }}</label>
                                                                <input class="form--control" type="text" name="faqs_title[]" placeholder="{{ __('Faq Title') }}">
                                                            </div>
                                                            <div class="single-info-input mt-3">
                                                                <label class="label_title">{{ __('Description') }}</label>
                                                                <textarea class="form--control textarea-input" name="faqs_description[]" cols="20" rows="2" placeholder="{{ __('Faq Description') }}"></textarea>
                                                            </div>
                                                            <!-- Default FAQ markup end -->

                                                            <!-- Container for dynamically added FAQ fields -->
                                                            <div class="faqs-container"></div>
                                                        </div>
                                                    </div>
                                                    <div class="btn-wrapper mt-3">
                                                        <a href="javascript:void(0)" class="btn-see-more style-02 color-3 add-faqs">{{ __('Add More') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Service Faqs End -->
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

                                            <div class="tab-pane fade show active" id="v-pills-home"
                                                 role="tabpanel" aria-labelledby="v-pills-home-tab">
                                                <div class="row g-4">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label class="label_title" for="title">{{__('Meta Title')}}</label>
                                                            <input type="text" class="form-control" name="meta_title" id="meta_title" placeholder="{{__('Title')}}" wire:model.defer="meta.meta_title">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="form-group" wire:ignore>
                                                            <label class="label_title" for="slug">{{__('Meta Tags')}}</label>
                                                            <input type="text" class="form-control" name="meta_tags" placeholder="Slug" data-role="tagsinput" id="meta_tags" wire:model.defer="meta.meta_tags">
                                                        </div>

                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label class="label_title" for="title">{{__('Meta Description')}}</label>
                                                            <textarea name="meta_description"  class="form-control textarea-input" cols="20" rows="2" wire:model.defer="meta.meta_description"
                                                            placeholder="{{ __('Description') }}"></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
                                                 aria-labelledby="v-pills-profile-tab">
                                                <div class="row g-4">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label class="label_title" for="title">{{__('Facebook Meta Title')}}</label>
                                                            <input type="text" class="form-control" placeholder="{{__('Title')}}"  wire:model.defer="meta.facebook_meta_tags" name="facebook_meta_tags">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label class="label_title" for="title">{{__('Facebook Meta Description')}}</label>
                                                            <textarea name="facebook_meta_description" class="form-control textarea-input" cols="20"
                                                                      rows="2" wire:model.defer="meta.facebook_meta_description" placeholder="{{ __('Description') }}"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label class="label_title" for="image">{{__('Facebook Meta Image')}}</label>
                                                            <div class="media-upload-btn-wrapper">
                                                                <div class="img-wrap"></div>
                                                                <input type="hidden"  name="facebook_meta_image" wire:model.defer="meta.facebook_meta_image" id="facebook_meta_image">
                                                                <button type="button"
                                                                        class="btn btn-info media_upload_form_btn"
                                                                        data-btntitle="{{__('Select Image')}}"
                                                                        data-modaltitle="{{__('Upload Image')}}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#media_upload_modal">
                                                                    {{__('Upload Image')}}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="v-pills-messages" role="tabpanel"
                                                 aria-labelledby="v-pills-messages-tab">
                                                <div class="row g-4">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label class="label_title" for="title">{{__('Twitter Meta Title')}}</label>
                                                            <input type="text" class="form-control" placeholder="{{__('Title')}}"
                                                                   name="twitter_meta_tags" wire:model.defer="meta.twitter_meta_tags" >
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label class="label_title" for="title">{{__('Twitter Meta Description')}}</label>
                                                            <textarea name="twitter_meta_description" class="form-control textarea-input" cols="20"
                                                                      rows="2" wire:model.defer="meta.twitter_meta_description" placeholder="{{ __('Description') }}"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label class="label_title" for="image">{{__('Twitter Meta Image')}}</label>
                                                            <div class="media-upload-btn-wrapper">
                                                                <div class="img-wrap"></div>
                                                                <input type="hidden" name="twitter_meta_image" wire:model.defer="meta.twitter_meta_image" id="twitter_meta_image">
                                                                <button type="button"
                                                                        class="btn btn-info media_upload_form_btn"
                                                                        data-btntitle="{{__('Select Image')}}"
                                                                        data-modaltitle="{{__('Upload Image')}}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#media_upload_modal">
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
                                                    <div class="img-wrap"></div>
                                                    <input type="hidden" name="image" id="service_image">
                                                    <button type="button" class="btn btn-info media_upload_form_btn"
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
                                            <div class="img-wrap"></div>
                                            <input type="hidden" name="image_gallery" id="image_gallery">
                                            <button type="button" class="btn btn-info media_upload_form_btn"
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
                                                <input class="hide_show_check_box_new custom-switch is_service_all_cities" id="is_service_all_cities" type="checkbox" wire:model.defer="services.is_service_all_cities"/>
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
                            wire:loading.class="btn-disabled"
                            wire:click.prevent="serviceStore">{{__('Submit')}}
                    </button>
                </div>
            </div>
        </div>
    </form>

<!-- for select -->
 @push('scripts')
     <script>
         $(document).ready(function () {
             let includeIndex = 1; // Track the index for new fields
             let additionalIndex = 1;
             let benefitIndex = 1;
             let faqIndex = 1;

             // Unbind any existing click handlers to prevent duplicates
             $('.add-more-service').off('click').on('click', function () {
                 includeIndex++; // Increment index

                 // Check if service is online to hide price and quantity fields
                 let isServiceOnline = $('#is_service_online').is(':checked');
                 let priceFieldClass = isServiceOnline ? 'd-none is_service_online_hide' : 'is_service_online_hide';
                 let quantityFieldClass = isServiceOnline ? 'd-none is_service_online_hide' : 'is_service_online_hide';

                 // Create new input field HTML
                 let newField = `
            <div class="single-dashboard-input what-include-element mt-4 include-service-field" data-index="${includeIndex}">
                <div class="row align-items-center g-4">
                    <div class="col-lg-4 col-sm-6">
                        <div class="single-info-input">
                            <label class="label_title">{{__('Title')}} <span class="text-danger">*</span></label>
                            <input class="form--control" type="text" name="include_service_title[]" placeholder="{{__('Service title')}}">
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 ${priceFieldClass}">
                        <div class="single-info-input">
                            <label class="label_title">{{__('Unit Price')}} <span class="text-danger">*</span></label>
                            <input class="form--control include-price" type="number" name="include_service_price[]" placeholder="{{__('Add Price')}}">
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 ${quantityFieldClass}">
                        <div class="single-info-input">
                            <label class="label_title">{{__('Quantity')}}</label>
                            <input class="form--control numeric-value" type="text" value="1" placeholder="{{__('Add Quantity')}}" readonly>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-6">
                        <button type="button" class="btn btn-danger remove-service mt-3"><i class="las la-times"></i></button>
                    </div>
                </div>
            </div>
        `;

                 // Append to container
                 $('.include-services-container').append(newField);

                 // Optional Ajax request
             });

             // Handle "Remove" button click
             $(document).on('click', '.remove-service', function () {
                 $(this).closest('.include-service-field').remove();

                 // Recalculate total price after removal
                 var sum = 0;
                 $(".include-price").each(function () {
                     if (!isNaN($(this).val()) && $(this).val() !== '') {
                         sum += parseFloat($(this).val());
                     }
                 });
                 $("#service_total_price").val(sum);
             });

             $('.add-additional-services').off('click').on('click', function () {
                 additionalIndex++;

                 // Create new input field HTML
                 let newField = `
            <div class="row g-4 mt-2 additional-service-element additional-service-field" data-index="${additionalIndex}">
                <div class="col-xl-3 col-sm-6">
                    <div class="single-info-input">
                        <label class="label_title">{{ __('Title') }} <span class="text-danger">*</span></label>
                        <input class="form--control" type="text" name="additional_service_title[]" placeholder="{{ __('Service title') }}">
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6">
                    <div class="single-info-input">
                        <label class="label_title">{{ __('Unit Price') }} <span class="text-danger">*</span></label>
                        <input class="form--control numeric-value" type="number" name="additional_service_price[]" placeholder="{{ __('Add Price') }}">
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6">
                    <div class="single-info-input">
                        <label class="label_title">{{ __('Quantity') }}</label>
                        <input class="form--control numeric-value" type="text" value="1" placeholder="{{ __('Add Quantity') }}" readonly>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="single-info-input">
                        <div class="form-group">
                            <div class="media-upload-btn-wrapper">
                                <div class="img-wrap mb-0"></div>
                                <input type="hidden" name="additional_service_image[${additionalIndex}]" class="additional_service_image">
                                <button data-value="${additionalIndex}" type="button" class="btn btn-info media_upload_form_btn"
                                        data-btntitle="{{ __('Select Image') }}"
                                        data-modaltitle="{{ __('Upload Image') }}" data-bs-toggle="modal"
                                        data-bs-target="#media_upload_modal">
                                    {{ __('Upload Image') }}
                 </button>
                 <small style="font-size: 10px">{{ __('image format: jpg,jpeg,png') }} <span> ({{ __('recommended size 78x78') }}) </span></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-2">
                    <button type="button" class="btn btn-danger remove-additional-service"><i class="las la-times"></i></button>
                </div>
            </div>
        `;

                 // Append to container
                 $('.additional-services-container').append(newField);
             });

             // Handle "Remove" for additional services
             $(document).on('click', '.remove-additional-service', function () {
                 $(this).closest('.additional-service-field').remove();
             });

             // Handle "Add More" for benefits
             $('.add-benefit').off('click').on('click', function () {
                 benefitIndex++;

                 // Create new benefit field HTML
                 let newField = `
            <div class="single-dashboard-input benifits benefit-field mt-3" data-index="${benefitIndex}">
                <div class="row align-items-center g-4">
                    <div class="col-xl-10 col-sm-9">
                        <div class="single-info-input">
                            <input class="form--control" type="text" name="benifits[]" placeholder="{{ __('Type Here') }}">
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-3">
                        <button type="button" class="btn btn-danger remove-benefit"><i class="las la-times"></i></button>
                    </div>
                </div>
            </div>
        `;

                 // Append to container
                 $('.benifits-container').append(newField);
             });

             // Handle "Remove" for benefits
             $(document).on('click', '.remove-benefit', function () {
                 $(this).closest('.benefit-field').remove();
             });

             // Handle "Add More" for FAQs
             $('.add-faqs').off('click').on('click', function () {
                 faqIndex++;

                 // Create new FAQ field HTML
                 let newField = `
            <div class="row faq-field mt-3" data-index="${faqIndex}">
                <div class="col-xl-10">
                    <div class="single-dashboard-input faqs">
                        <div class="single-info-input mt-3">
                            <label class="label_title">{{ __('Title') }}</label>
                            <input class="form--control" type="text" name="faqs_title[]" placeholder="{{ __('Faq Title') }}">
                        </div>
                        <div class="single-info-input mt-3">
                            <label class="label_title">{{ __('Description') }}</label>
                            <textarea class="form--control textarea-input" name="faqs_description[]" cols="20" rows="2" placeholder="{{ __('Faq Description') }}"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2">
                    <button type="button" class="btn btn-danger remove-faq mt-3"><i class="las la-times"></i></button>
                </div>
            </div>
        `;

                 // Append to container
                 $('.faqs-container').append(newField);

                 // Apply faq_show_hide based on is_service_online
                 if ($('#is_service_online').is(':checked')) {
                     $('.faq-field[data-index="${faqIndex}"]').addClass('faq_show_hide').show();
                 } else {
                     $('.faq-field[data-index="${faqIndex}"]').addClass('faq_show_hide').hide();
                 }
             });

             // Handle "Remove" for FAQs
             $(document).on('click', '.remove-faq', function () {
                 $(this).closest('.faq-field').remove();
             });

             // Handle is_service_online toggle
             $('#is_service_online').on('change', function () {
                 if ($(this).is(':checked')) {
                     $('.is_service_online_hide').addClass('d-none');
                     $('.faq_show_hide').show();
                     $('.service-price-show-hide').hide();
                 } else {
                     $('.is_service_online_hide').removeClass('d-none');
                     $('.faq_show_hide').hide();
                     $('.service-price-show-hide').show();
                 }
             });

             // Initialize visibility based on checkbox state
             if ($('#is_service_online').is(':checked')) {
                 $('.is_service_online_hide').addClass('d-none');
                 $('.faq_show_hide').show();
                 $('.service-price-show-hide').hide();
             } else {
                 $('.is_service_online_hide').removeClass('d-none');
                 $('.faq_show_hide').hide();
                 $('.service-price-show-hide').show();
             }

             // Handle tab navigation
             $(document).on('click', '#add-service-tab .nav-link', function (e) {
                 let el = $(this);
                 $('input[name="current_tab"]').val(el.attr('id'));
             });

             $(document).on('click', '#service-attribute-tab', function (e) {
                 $('.service_tab_hide_show').removeClass('d-none');
             });

             // Replace Livewire @this.set with hidden inputs or Ajax
             $('#category').on('change', function () {
                 $('input[name="category"]').val($(this).val());
                 // Optional: Ajax to update server
             });

             $('#subcategory').on('change', function () {
                 $('input[name="subcategory"]').val($(this).val());
                 // Optional: Ajax
             });

             $('#child_category').on('change', function () {
                 $('input[name="child_category"]').val($(this).val());
                 // Optional: Ajax
             });

             $('#title').on('keyup input', function () {
                 $('input[name="services[slug]"]').val($(this).val());
                 // Optional: Ajax
             });

             // Summernote for description
             // Initialize jodit editor
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
                         'hr', 'eraser', 'fullsize'
                     ],
                     "uploader": {
                         "insertImageAsBase64URI": true
                     },
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
             // Meta tags
             $('#meta_tags').on('change', function () {
                 $('input[name="meta[meta_tags]"]').val($(this).val());
                 // Optional: Ajax
             });

             // Media uploads
             $(document).on('click', '.media_upload_modal_submit_btn', function () {

             });
         });
     </script>
 @endpush
