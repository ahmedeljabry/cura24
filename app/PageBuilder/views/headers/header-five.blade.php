<!-- Banner area Starts -->
<div class="new_banner_area new-section-bg padding-top-100 padding-bottom-100" data-padding-top="{{$padding_top}}" data-padding-bottom="{{$padding_bottom}}"
     style="background-color: {{$header_background_color}}">
    <div class="container">
        <div class="row g-5 align-items-center justify-content-between">
            <div class="col-xl-6 col-lg-7">
                <div class="new_banner__contents">
                    <h2 class="new_banner__contents__title">{{$title_start}} <span class="color-three"> {{$title_end}} </span> </h2>
                    <p class="new_banner__contents__para mt-4">{{$subtitle}}</p>
                    <div class="new_banner__search mt-4 mt-lg-5">

                        @if(!empty(get_static_option('google_map_settings')))
                            <!--google map -->
                            <form action="{{get_static_option('select_home_page_search_service_page_url') ?? \Mcamara\LaravelLocalization\Facades\LaravelLocalization::localizeURL('/service-list')}}" class="new_banner__search__form banner-search-location" method="get">
                                <div class="new_banner__search__input">
                                    <div class="new_banner__search__location_left" id="myLocationGetAddress">
                                        <i class="fa-solid fa-location-crosshairs"></i>
                                    </div>
                                    <input class="form--control" name="change_address_new" id="change_address_new" type="hidden" value="">
                                    <input class="form--control" name="autocomplete" id="autocomplete" type="text" placeholder="{{ get_static_option('google_map_search_placeholder_title') ?? __('Search location here') }}">
                                </div>
                                <button type="submit" class="new_banner__search__button setLocation_btn">{{ get_static_option('google_map_search_button_title') ?? __('Set Location') }}</button>
                            </form>
                        @else
                            <form action="{{get_static_option('select_home_page_search_service_page_url') ?? \Mcamara\LaravelLocalization\Facades\LaravelLocalization::localizeURL('/service-list')}}" method="get" class="new_banner__search__form mt-4">
                                <div class="new_banner__search__input">
                                    <input class="form--control" type="text" name="home_search" id="home_search" placeholder="{{ __('What are you looking for?') }}">
                                </div>
                                <button type="submit" class="new_banner__search__button">{{ __('Search Service') }}</button>
                            </form>
                            <span id="all_search_result"></span>
                        @endif
                    </div>

                    @if(!empty($satisfied_customer_show_hide))
                        <div class="new_banner__reviewer mt-4">
                            <div class="new_banner__reviewer__flex d-flex">
                                @foreach ($satisfied_customer_images['satisfied_customer_image_'] ?? [] as $key => $customer_image)
                                    <div class="new_banner__reviewer__thumb">
                                        <a href="javascript:void(0)">
                                            {!! render_image_markup_by_attachment_id($customer_image) !!}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            <h4 class="new_banner__reviewer__title"><a href="javascript:void(0)">{{ $satisfied_customer_title }}</a></h4>
                        </div>
                    @endif

                    <div class="btn-wrapper btn_flex mt-4">
                        @if(!empty($button_one_show_hide))
                            <a href="{{ $button_one_link }}" class="cmn-btn btn-outline-2 radius-5">{{ $button_one_title }}</a>
                        @endif
                        @if(!empty($button_two_show_hide))
                            <a href="{{ $button_two_link }}" class="cmn-btn btn-bg-2 radius-5">{{ $button_two_title }}</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-5">
                <div class="new_banner__wrapper">
                    <div class="new_banner__thumb">
                        <div class="new_banner__thumb__flex">
                            <div class="new_banner__thumb__item">
                                <div class="new_banner__thumb__main">
                                    {!! $image_two !!}
                                </div>
                            </div>
                            <div class="new_banner__thumb__item">
                                <div class="new_banner__thumb__main">  {!! $image_one !!} </div>
                                @if(!empty($review_banner_show_hide))
                                    <div class="new_banner__thumb__contents d-flex">
                                        <div class="new_banner__thumb__contents__icon">
                                            <i class="{{$review_icon ?? 'fa-solid fa-thumbs-up'}}"></i>
                                        </div>
                                        <p class="new_banner__thumb__contents__para">{{$five_star_review_clients_count}}+ {{ $review_title ?? __('5 Star Reviews') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Banner area end -->

@section('scripts')
    <script>
        (function($){
            "use strict";

            // This function will be called when the Google Maps API is loaded
            window.initLocationAutocomplete = function() {
                // Get the correct keys from localStorage
                let userLat = localStorage.getItem('latitude');
                let userLng = localStorage.getItem('longitude');
                let locationUpdated = false; // Flag to track if location has been set

                // If we have both latitude and longitude, perform reverse geocoding
                if (userLat && userLng) {
                    getLocationName(userLat, userLng);
                    locationUpdated = true;
                }

                // Set up a storage event listener to detect changes in localStorage
                window.addEventListener('storage', function(e) {
                    if (e.key === 'latitude' || e.key === 'longitude') {
                        // Get the updated values
                        const updatedLat = localStorage.getItem('latitude');
                        const updatedLng = localStorage.getItem('longitude');

                        // If we now have both values, update the location
                        if (updatedLat && updatedLng && !locationUpdated) {
                            getLocationName(updatedLat, updatedLng);
                            locationUpdated = true;
                        }
                    }
                });

                // Set up a more efficient periodic check with a maximum number of attempts
                let checkCount = 0;
                const maxChecks = 30; // Check for 1 minute (30 * 2 seconds)

                const locationChecker = setInterval(function() {
                    checkCount++;

                    // Stop checking after max attempts or if location is already set
                    if (checkCount >= maxChecks || locationUpdated) {
                        clearInterval(locationChecker);
                        return;
                    }

                    const currentLat = localStorage.getItem('latitude');
                    const currentLng = localStorage.getItem('longitude');

                    // If we have values now but didn't before, update the location
                    if (currentLat && currentLng && (!userLat || !userLng)) {
                        getLocationName(currentLat, currentLng);
                        userLat = currentLat;
                        userLng = currentLng;
                        locationUpdated = true;

                        // Clear the interval since we found what we needed
                        clearInterval(locationChecker);
                    }
                }, 2000);
            };

            function getLocationName(lat, lng) {
                const geocoder = new google.maps.Geocoder();
                const latlng = new google.maps.LatLng(parseFloat(lat), parseFloat(lng));

                geocoder.geocode({'location': latlng}, function(results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            // Build a comprehensive location name
                            let locationParts = [];
                            let neighborhood = '';
                            let city = '';
                            let state = '';
                            let country = '';

                            // Extract all relevant components
                            for (let i = 0; i < results[0].address_components.length; i++) {
                                const component = results[0].address_components[i];

                                if (component.types.includes('sublocality') ||
                                    component.types.includes('neighborhood') ||
                                    component.types.includes('administrative_area_level_3')) {
                                    neighborhood = component.long_name;
                                }

                                if (component.types.includes('locality') ||
                                    component.types.includes('administrative_area_level_2')) {
                                    city = component.long_name;
                                }

                                if (component.types.includes('administrative_area_level_1')) {
                                    state = component.long_name;
                                }

                                if (component.types.includes('country')) {
                                    country = component.long_name;
                                }
                            }

                            // Build the location name with all available parts
                            if (neighborhood) locationParts.push(neighborhood);
                            if (city) locationParts.push(city);
                            if (state) locationParts.push(state);
                            if (country) locationParts.push(country);

                            const locationName = locationParts.join(', ');

                            // Set the location name in the search input
                            if (locationName && document.getElementById('autocomplete')) {
                                document.getElementById('autocomplete').value = locationName;

                                // Also update the hidden field
                                if (document.getElementById('change_address_new')) {
                                    document.getElementById('change_address_new').value = results[0].formatted_address;
                                }
                            }
                        }
                    }
                });
            }

            $(document).ready(function(){
                // Wait a bit for localStorage to be populated
                setTimeout(function() {
                    // If Google Maps API is already loaded, initialize immediately
                    if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                        initLocationAutocomplete();
                    }
                }, 1000); // Wait 1 second
            });
        })(jQuery);
    </script>
@endsection