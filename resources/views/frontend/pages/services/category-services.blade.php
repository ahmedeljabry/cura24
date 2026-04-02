@extends('frontend.frontend-page-master')

@section('site-title')
@if($category !='')
{{ $category->name }}
@endif
@endsection

@section('page-title')
@if($category !='')
{{ $category->name }}
@endif
@endsection
@section('page-meta-data')
    {!!  render_page_meta_data_for_category($category) !!}
@endsection
@section('inner-title')
@if($category !='')
{{ $category->name }}
@endif

@endsection 

@section('content')
    <section class="category-services-area padding-top-70 padding-bottom-100">
        <div class="container">

            <div class="row">
                <div class="col-lg-12 mb-3">
                    <div class="category_info_new">
                        {!! $category->description !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Sidebar (Col-3) -->
                <div class="col-lg-3">
                    <div class="mb-5 sub-category-sidebar">
                        <h4 class="section-title mb-4" style="font-size: 20px; font-weight: 600;">{{ sprintf(__('Subcategories in') .' '.'%s', $category->name)  }}</h4>
                        <div id="services_sub_category_load_wrap">
                            <ul class="list-group" style="box-shadow: 0 0 15px rgba(0,0,0,0.05); border-radius: 10px; overflow: hidden;">
                            @foreach($subcategory_under_category as $sub_cat)
                                <li class="list-group-item d-flex justify-content-between align-items-center" style="border: none; border-bottom: 1px solid #f1f1f1; padding: 15px 20px;">
                                    <a href="{{ route('service.list.subcategory',$sub_cat->slug) }}" style="color: #333; font-weight: 500; text-decoration: none;">{{ $sub_cat->name }}</a>
                                    <span class="badge badge-primary badge-pill" style="background-color: var(--main-color-one); font-size: 13px; font-weight: 500; padding: 6px 12px; border-radius: 20px;">{{ $sub_cat->total_service }}</span>
                                </li>
                            @endforeach
                            </ul>

                            @if(!empty(get_static_option('load_more_button_show_hide_settings')) || $subcategory_under_category->count() > 20)
                                <div class="load_more_button_warp mt-4" style="text-align: center;">
                                    <a href="#" id="load_more_btn" data-total="20" class="cmn-btn btn-small btn-bg-1" style="width: 100%;">{{__('Load More')}}</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Content (Col-9) -->
                <div class="col-lg-9">
                    <div class="row">
                <div class="col-lg-12">
                    <h2 class="section-sub-title margin-top-80">{{ sprintf(__('Available Services in') . ' ' . '%s', $category->name) }}</h2>
                    @php $current_page_url = url()->current(); @endphp
                    <div class="category-service-search-form margin-top-50">
                        <form method="get" action="{{ $current_page_url }}" id="search_service_list_form">
                            <div class="row">
                                <div class="col-lg-3 col-sm-6 mb-3">
                                    <div class="form-group">
                                        <input type="text" class="search-input form-control" id="search_by_query"
                                               placeholder="{{ __('write minimum 3 character to search') }}"
                                               name="q" value="{{ request()->get('q') }}">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 mb-3">
                                    <div class="single-category-service">
                                        <div class="single-select">
                                            <select id="search_by_rating" name="rating">
                                                <option value="">{{ __('Select Rating Star') }}</option>
                                                <option value="1"
                                                        @if (!empty(request()->get('rating')) && request()->get('rating') == 1) selected @endif>{{ __('One Star') }}</option>
                                                <option value="2"
                                                        @if (!empty(request()->get('rating')) && request()->get('rating') == 2) selected @endif>{{ __('Two Star') }}</option>
                                                <option value="3"
                                                        @if (!empty(request()->get('rating')) && request()->get('rating') == 3) selected @endif>{{ __('Three Star') }}</option>
                                                <option value="4"
                                                        @if (!empty(request()->get('rating')) && request()->get('rating') == 4) selected @endif>{{ __('Four Star') }}</option>
                                                <option value="5"
                                                        @if (!empty(request()->get('rating')) && request()->get('rating') == 5) selected @endif>{{ __('Five Star') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 mb-3">
                                    <div class="single-category-service flex-category-service">
                                        <div class="single-select">
                                            <select id="search_by_location" name="location">
                                                <option value="">{{ __('Select Location') }}</option>
                                                @foreach ($cities as $city)
                                                    <option value="{{ $city->id }}"
                                                            @if (!empty(request()->get('location')) && request()->get('location') == $city->id) selected @endif>
                                                        {{ __($city->service_city) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 mb-3">
                                    <div class="single-category-service flex-category-service">
                                        <div class="single-select">
                                            <select id="search_by_sorting" name="sortby">
                                                <option value="">{{ __('Sort By') }}</option>
                                                <option value="latest_service"
                                                        @if (!empty(request()->get('sortby')) && request()->get('sortby') == 'latest_service') selected @endif>{{ __('Latest Service') }}</option>
                                                <option value="lowest_price"
                                                        @if (!empty(request()->get('sortby')) && request()->get('sortby') == 'lowest_price') selected @endif>{{ __('Lowest Price') }}</option>
                                                <option value="highest_price"
                                                        @if (!empty(request()->get('sortby')) && request()->get('sortby') == 'highest_price') selected @endif>{{ __('Highest Price') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @if($all_services->count() >= 1)
                    @foreach($all_services as $service)
                        
                        <div class="col-lg-4 col-md-6 margin-top-30 all-services">
                            <div class="single-service no-margin wow fadeInUp" data-wow-delay=".2s">
                                <a href="{{ route('service.list.details',$service->slug) }}" class="service-thumb service-bg-thumb-format" {!! render_background_image_markup_by_attachment_id($service->image) !!}>

                                    @if($service->featured == 1)
                                    <div class="award-icons">
                                        <i class="las la-award"></i>
                                    </div>
                                    @endif
                                    <div class="country_city_location">
                                        <span class="single_location"> <i class="las la-map-marker-alt"></i>
                                            {{ sellerServiceLocation($service) }}
                                        </span>
                                    </div>
                                </a>
                                <div class="services-contents">
                                    <ul class="author-tag">
                                        <li class="tag-list">
                                            <a href="{{ route('about.seller.profile',optional($service->seller)->username) }}">
                                                <div class="authors">
                                                    <div class="thumb">
                                                        {!! render_image_markup_by_attachment_id(optional($service->seller)->image) !!}
                                                        <span class="notification-dot"></span>
                                                    </div>
                                                    <span class="author-title"> {{ optional($service->seller)->name }} </span>
                                                </div>
                                            </a>
                                        </li>

                                        @if($service->reviews->where('type', 1)->count() >= 1)
                                        <li class="tag-list">
                                            <a href="javascript:void(0)">
                                                <span class="reviews">
                                                    {!! ratting_star(round(optional($service->reviews->where('type', 1))->avg('rating'),1)) !!}
                                                    ({{ optional($service->reviews->where('type', 1))->count() }})
                                                </span>
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                    <h5 class="common-title"> <a href="{{ route('service.list.details',$service->slug) }}"> {{ Str::limit($service->title) }} </a> </h5>
                                    <p class="common-para"> {{ Str::limit(strip_tags($service->description),100) }} </p>
                                    <div class="service-price">
                                        <span class="starting"> {{ __('Starting at') }} </span>
                                        <span class="prices"> {{ amount_with_currency_symbol($service->price) }} </span>
                                    </div>
                                    <div class="btn-wrapper d-flex flex-wrap">
                                        <a href="{{ route('service.list.book',$service->slug) }}" class="cmn-btn btn-small btn-bg-1"> {{ __('Book Now') }} </a>
                                        <a href="{{ route('service.list.details',$service->slug) }}" class="cmn-btn btn-small btn-outline-1 ml-auto"> {{ __('View Details') }} </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($all_services->count() >= 9)
                        <div class="col-lg-12">
                            <div class="blog-pagination margin-top-55">
                                <div class="custom-pagination mt-4 mt-lg-5">
                                    {!! $all_services->links() !!}
                                </div>
                            </div>
                        </div>
                    @endif
                @else 
                    <div class="alert alert-warning">{{sprintf(__('No services found in').' '.'%s', optional($category)->name)}}</div>
                @endif

            </div> <!-- End Right Content row -->
            </div> <!-- End Right Content Col-9 -->

        </div>

    </section>

@endsection
@section("scripts")
      <script>
      	(function($){
          "use strict";
          
          $(document).on('click','#load_more_btn',function(e){
            e.preventDefault();
            
            let totalNo = $(this).data('total');
            let el = $(this);
            let container = $('#services_sub_category_load_wrap > .row');
            
            $.ajax({
              type: "POST",
              url: "{{route('service.list.load.more.subcategories')}}",
              beforeSend: function(e){
                el.text("{{__('loading...')}}")
              },
              data : {
                _token: "{{csrf_token()}}",
                total: totalNo,
                catId: "{{$category->id}}"
              },
              success: function(data){
                
                 el.text("{{__('Load More')}}");
                if(data.markup === ''){
                  el.hide();
                  container.append("<div class='col-lg-12'><div class='text-center text-warning mt-3'>{{__('no more subcategory found')}}</div></div>"); 
                  return;
                }
                
                $('#load_more_btn').data('total',data.total);
                 
                container.append(data.markup); 
              }
            });
            
          });
          
          
        })(jQuery);
      
      </script>
@endsection
