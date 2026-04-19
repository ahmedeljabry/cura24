@extends('backend.admin-master')

@section('site-title')
    {{__('Add New Country')}}
@endsection
@section('content')
    <div class="col-lg-6 col-ml-12 padding-bottom-30">
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
                                <h4 class="header-title">{{__('Add New Country')}} </h4>
                            </div>
                            <div class="right-content">
                                <a class="btn btn-info btn-sm" href="{{route('admin.country')}}">{{__('All Countries')}}</a>
                            </div>
                        </div>
                        <form action="{{route('admin.country.add')}}" method="post">
                            @csrf
                            <div class="tab-content margin-top-40">
                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#country-it" role="tab" style="color: blue">{{__('Italian (Default)')}}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#country-en" role="tab" style="color: blue">{{__('English')}}</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="country-it" role="tabpanel">
                                        <div class="form-group">
                                            <label for="country">{{__('Service Country (Italian)')}}</label>
                                            <input type="text" class="form-control" name="country" id="country" placeholder="{{__('Service Country')}}">
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="country-en" role="tabpanel">
                                        <div class="form-group">
                                            <label for="country_en">{{__('Service Country (English)')}}</label>
                                            <input type="text" class="form-control" name="country_en" id="country_en" placeholder="{{__('Service Country')}}">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3 submit_btn">{{__('Submit')}}</button>
                              </div>
                        </form>
                   </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')

    <script>
        <x-icon-picker/> 
        (function ($) {
            "use strict";
            $(document).ready(function () {
               
            });
        })(jQuery);
    </script>
@endsection

