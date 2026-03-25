@extends('backend.admin-master')

@section('site-title')
    {{__('Register Recaptch Settings Page')}}
@endsection

@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-6 mt-5">
                <x-msg.success/>
                <x-msg.error/>
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-4">{{__("Google Captcha Settings")}}</h4>
                        <form action="{{route('admin.captcha.page.settings')}}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="login_form_title">{{__('Google Recaptcha v2 (Site Key)')}}</label>
                                <input type="text" name="site_google_captcha_v2_site_key" class="form-control" value="{{get_static_option('site_google_captcha_v2_site_key')}}" id="fleid">
                            </div>
                            
                             <div class="form-group">
                                <label for="login_form_title">{{__('Google Recaptcha v2 (Secret Key)')}}</label>
                                <input type="text" name="site_google_captcha_v2_secret_key" class="form-control" value="{{get_static_option('site_google_captcha_v2_secret_key')}}" id="fleid">
                            </div>
                            
                            <div class="form-group">
                                <label for="login_form_title">{{__('Google Recaptcha v3 (Site Key)')}}</label>
                                <input type="text" name="site_google_captcha_v3_site_key" class="form-control" value="{{get_static_option('site_google_captcha_v3_site_key')}}" id="fleid">
                            </div>
                            
                            <div class="form-group">
                                <label for="login_form_title">{{__('Google Recaptcha v3 (Secret Key)')}}</label>
                                <input type="text" name="site_google_captcha_v3_secret_key" class="form-control" value="{{get_static_option('site_google_captcha_v3_secret_key')}}" id="fleid">
                            </div>

                           
                            <button type="submit" id="update" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update Changes')}}</button>
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
            $(document).ready(function(){
                <x-icon-picker/>
                <x-btn.update/>
            });
        }(jQuery));
    </script>
@endsection
