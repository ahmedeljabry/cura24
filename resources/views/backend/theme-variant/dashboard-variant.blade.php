@extends('backend.admin-master')
@section('site-title')
    {{__('Seller/Buyer Panel Settings')}}
@endsection

@section('style')
    <x-datatable.css/>
@endsection

@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40"></div>
                <x-msg.success />
                <x-msg.error />
            </div>
            <div class="col-lg-6 mt-5">
                <div class="card">
                    <div class="card-body">
                        <div class="header-wrap d-flex justify-content-between">
                            <div class="left-content">
                                <h4 class="header-title">{{ __('Seller/Buyer Dashboard Settings') }}</h4>
                            </div>
                        </div>

                        <form action="{{ route('admin.dashboard.variant') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="start_week_from">{{ __('Select Start Day') }}</label>
                                @php $days_array = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; @endphp
                                <select name="start_week_from" class="form-control">
                                    @foreach($days_array as $key => $day_name)
                                        <option value="{{ $key }}" @if(get_static_option('start_week_from') == $key) selected @endif>{{ $day_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="timezone">{{ __('Select Time Zone') }}</label>
                                <select name="timezone" class="form-control">
                                    @foreach(timezone_identifiers_list() as $timezone)
                                        <option value="{{ $timezone }}" @if(get_static_option('timezone') == $timezone) selected @endif>{{ $timezone }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="time_format">{{ __('Time Format') }}</label>
                                <select name="time_format" class="form-control">
                                    <option value="12" @if(get_static_option('time_format') == '12') selected @endif>12-Hour (e.g., 1:30 PM)</option>
                                    <option value="24" @if(get_static_option('time_format') == '24') selected @endif>24-Hour (e.g., 13:30)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="date_format">{{ __('Date Format') }}</label>
                                <select name="date_format" class="form-control">
                                    @php
                                        $date_formats = [
                                            'Y-m-d' => 'YYYY-MM-DD (e.g., 2025-05-24)',
                                            'd/m/Y' => 'DD/MM/YYYY (e.g., 24/05/2025)',
                                            'm/d/Y' => 'MM/DD/YYYY (e.g., 05/24/2025)',
                                            'd-m-Y' => 'DD-MM-YYYY (e.g., 24-05-2025)',
                                            'M d, Y' => 'MMM DD, YYYY (e.g., May 24, 2025)',
                                        ];
                                    @endphp
                                    @foreach($date_formats as $format => $example)
                                        <option value="{{ $format }}" @if(get_static_option('date_format') == $format) selected @endif>{{ $example }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button id="update" type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{ __('Update') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        (function($){
            "use strict";

            $(document).ready(function () {
                <x-btn.update/>

                let imgSelect = $('.img-select');
                let id = $('#dashboard_variant_buyer').val();
                imgSelect.removeClass('selected');
                $('img[data-dashboard_id="'+id+'"]').parent().parent().addClass('selected');

                // buyer dashboard
                $(document).on('click','.img-select img',function (e) {
                    e.preventDefault();
                    imgSelect.removeClass('selected');
                    $(this).parent().parent().addClass('selected').siblings();
                    $('#dashboard_variant_buyer').val($(this).data('dashboard_id'));
                });

            });

        })(jQuery);
    </script>
@endsection