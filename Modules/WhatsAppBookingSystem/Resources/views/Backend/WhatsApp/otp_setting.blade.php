@extends('backend.admin-master')


@section('site-title')
    {{__('Whatsapp OTP Settings')}}
@endsection

@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-12 mt-5">
                <x-msg.success/>
                <x-msg.error/>
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-4">{{__("Whatsapp OTP Settings")}}</h4>
                        <form action="{{route('admin.whatsapp.otp.setting.update')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="otp_template_name">{{__('OTP Template Name')}}</label>
                                <input type="text" name="otp_template_name"  class="form-control" value="{{get_static_option('otp_template_name')}}" id="otp_template_name" placeholder="user_otp_verify">
                            </div>

                            <div class="form-group">
                                <label for="enable_whatsapp_otp_message"><strong>{{__('Enable Whatsapp OTP Message')}}</strong></label>
                                <label class="switch yes">
                                    <input type="checkbox" name="enable_whatsapp_otp_message"  @if(!empty(get_static_option('enable_whatsapp_otp_message'))) checked @endif id="enable_whatsapp_otp_message">
                                    <span class="slider-enable-disable"></span>
                                </label>
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

                $(document).on("change","#disable_user_email_verify",function (){
                    let current_value = $("#disable_user_email_verify").is(':checked');
                    if(current_value == false){
                        $("#disable_user_otp_verify").prop("checked", true)
                    }
                });

                $(document).on("change","#disable_user_otp_verify",function (){
                    let current_value = $("#disable_user_otp_verify").is(':checked');
                    if(current_value == false){
                        $("#disable_user_email_verify").prop("checked", true)
                    }
                });

                // Show/hide reCAPTCHA version dropdown based on enable/disable toggle
                $(document).on("change", "#site_google_captcha_enable", function () {
                    let isEnabled = $(this).is(':checked');
                    $("#captcha_version_wrapper").toggle(isEnabled);
                });

            });
        }(jQuery));
    </script>
@endsection
