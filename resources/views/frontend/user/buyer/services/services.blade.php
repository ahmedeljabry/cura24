@extends('frontend.user.buyer.buyer-master')
@section('site-title')
    {{__('Services')}}
@endsection
@section('content')
    <x-frontend.seller-buyer-preloader/>
    @include('frontend.user.buyer.partials.sidebar-two')
    <div class="dashboard__right">
        @include('frontend.user.buyer.header.buyer-header')
        <div class="dashboard__body">
            <div class="dashboard__inner">
                <x-msg.error/>
                <x-msg.success/>

                <!-- search section start-->
                <div class="dashboard__inner__item dashboard_border padding-20 radius-10 bg-white">
                    <div class="dashboard__wallet">
                        <form action="{{ route('buyer.services') }}" method="GET">
                            <div class="dashboard__headerGlobal__flex">
                                <div class="dashboard__headerGlobal__content">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <h4 class="dashboard_table__title">{{ __('Search Service Module') }}</h4> <i class="las la-angle-down search_by_all"></i>
                                    </button>
                                </div>
                                <div class="dashboard__headerGlobal__btn">
                                    <div class="btn-wrapper">
                                        <button href="#" class="dashboard_table__title__btn btn-bg-1 radius-5" type="submit">
                                            <i class="fa-solid fa-magnifying-glass"></i> {{ __('Search') }}</button>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <div id="collapseOne" class="accordion-collapse collapse
                                @if(request()->get('service_title')) show
                                @elseif(request()->get('category')) show
                                @elseif(request()->get('online_offline_status')) show
                                @elseif(request()->get('service_price')) show
                                @endif
                             " aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="single-settings">
                                                    <div class="single-dashboard-input">
                                                        <div class="row g-4 mt-3">

                                                            <div class="col-lg-4 col-sm-6">
                                                                <div class="single-info-input">
                                                                    <label for="service_title" class="info-title"> {{__('Service Title')}} </label>
                                                                    <input class="form--control" name="service_title" value="{{ request()->get('service_title') }}" type="text" placeholder="{{ __('Service Title') }}">
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-4 col-sm-6">
                                                                <div class="single-info-input">
                                                                    <label for="category" class="info-title"> {{__('Category')}} </label>
                                                                    <select name="category">
                                                                        <option value="">{{__('Select Category')}}</option>
                                                                        @foreach($categories as $category)
                                                                            <option value="{{ $category->id }}" @if(request()->get('category') == $category->id) selected @endif>{{ $category->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-4 col-sm-6">
                                                                <div class="single-info-input">
                                                                    <label for="online_offline_status" class="info-title"> {{__('Service Type')}} </label>
                                                                    <select name="online_offline_status">
                                                                        <option value="">{{__('Select Type')}}</option>
                                                                        <option value="online" @if(request()->get('online_offline_status') == 'online') selected @endif>{{ __('Online') }}</option>
                                                                        <option value="offline" @if(request()->get('online_offline_status') == 'offline') selected @endif>{{ __('Offline') }}</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-4 col-sm-6">
                                                                <div class="single-info-input">
                                                                    <label for="service_price" class="info-title"> {{__('Service Price')}} </label>
                                                                    <input class="form--control" name="service_price" value="{{ request()->get('service_price') }}" type="number" placeholder="{{ __('Service Price') }}">
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
                        </form>
                    </div>
                </div>
                <!--search section end-->

                <div class="dashboard__headerContents">
                    <div class="dashboard__headerContents__flex">
                        <div class="dashboard__headerContents__left">
                            <h4 class="dashboard_table__title"> {{ __('All Services') }} </h4>
                        </div>
                    </div>
                </div>

                @if($services->count() > 0)
                    @foreach($services as $data)
                        <div class="dashboard__inner__item dashboard_border padding-20 radius-10 bg-white">
                            <div class="rows dash-single-inner">
                                <div class="dash-left-service">
                                    <div class="dashboard-services">
                                        <div class="dashboar-flex-services">
                                            <div class="thumb bg-image" {!! render_background_image_markup_by_attachment_id($data->image,'','thumb') !!}>
                                            </div>

                                            <div class="thumb-contents">
                                                <h4 class="title"> <a href="javascript:void(0)"> {{ $data->title }} </a> </h4>
                                                <div class="thumb-contents-review-flex">
                                                    <div class="thumb-contents-review-inner">

                                                        @if($data->is_service_online == 1)
                                                            <span class="service-review style-02"> <i class="las la-map-marker"></i> {{ __('Online') }} </span>
                                                        @else
                                                            <span class="service-review style-02"> <i class="las la-map-marker"></i> {{ __('Offline') }} </span>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="dash-righ-service">
                                    <div class="dashboard-switch-flex-content">
                                        <div class="dashboard-switch-single">
                                            <span class="dashboard-starting"> {{__('Starting From:')}} </span>
                                            <h2 class="title-price color-3"> {{ amount_with_currency_symbol($data->price)}} </h2>
                                        </div>

                                        <div class="dashboard-switch-single">
                                            <a href="{{route('service.list.details',$data->slug ?? 'x')}}" class="btn btn-primary">{{__('Book Now')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="blog-pagination margin-top-55">
                        <div class="custom-pagination mt-4 mt-lg-5">
                            {!! $services->links() !!}
                        </div>
                    </div>
                @else
                    <div class="chat_wrapper__details__inner__chat__contents">
                        <h2 class="chat_wrapper__details__inner__chat__contents__para">{{ __('No Service Created Yet') }}</h2>
                    </div>
                @endif

            </div>
        </div>
        @endsection
        @section('scripts')
            <script src="{{asset('assets/backend/js/sweetalert2.js')}}"></script>
            <script>
                (function($){
                    "use strict";

                    $(document).ready(function(){

                        // date range
                        $('.flatpickr_input').flatpickr({
                            altFormat: "invisible",
                            altInput: false,
                            mode: "range",
                        });

                    });

                })(jQuery);
            </script>
@endsection
