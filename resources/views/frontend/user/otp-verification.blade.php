@extends('frontend.frontend-master')
@section('page-meta-data')
    <title>{{__('Verify Account')}}</title>
@endsection
@section('style')
    <style>
        .timer {
            font-size: 16px;
            text-align: center;
            margin-bottom: 20px;
        }
        .timer #counter {
            font-weight: bold;
        }
    </style>
@endsection
@section('content')
    <div class="signup-area padding-top-70 padding-bottom-100">
        <div class="container">
            <div class="signup-wrapper">
                <div class="signup-contents">
                    <h3 class="signup-title"> {{ __('Verify Your Account')}} </h3>
                    @php
                         $twilioSid = env('TWILIO_SID');
                         $twilioAuthToken = env('TWILIO_AUTH_TOKEN');
                         $twilioNumber = env('TWILIO_NUMBER');
                         // Check if Twilio credentials are empty
                         if (empty($twilioSid) || empty($twilioAuthToken) || empty($twilioNumber)) {
                             $opt_empty_message = false;
                         }else{
                             $opt_empty_message = true;
                         }
                    @endphp

                      @if($opt_empty_message === false)
                        <div class="alert alert-danger alert-dismissible fade show mt-5 mb-1" role="alert">
                            {{ __('Oops! It appears that we are currently unable to send verification codes. Please try again later or get help from admin.') }}
                        </div>
                    @else
                        <div class="alert alert-info alert-dismissible fade show mt-5 mb-1" role="alert">
                            {{__('OTP has been sent on Your Phone Number.')}}
                        </div>
                     @endif

                    <div class="mt-2">
                        <x-session-msg/>
                        <x-msg.error/>
                    </div>

                    @if($opt_empty_message === false)
                    <div class="timer">  </div>
                    @else
                        <div class="timer">
                            <span id="counter">{{ __('00:00') }}</span> <br>
                            <small id="counter">{{ __('OTP Expire Time') }}</small> <br>
                        </div>
                    @endif

                    <form class="signup-forms"  @if(!empty($user_details))  action="{{ route('user.login.with.otp.code')}}"  @else  action="{{ route('email.verify')}}"  @endif  method="post">
                        @csrf
                        @if(empty($user_details))
                            <input type="hidden" name="user_id" value="{{$user_id}}" />
                        @else
                            <input type="hidden" name="user_id" value="{{$user_details->id}}" />
                        @endif

                        <div class="single-signup margin-top-30">
                            <label class="signup-label"> {{__('Enter OTP Code')}} <span class="text-danger">*</span> </label>
                            <input id="check_opt_send_login" type="hidden" name="check_opt_send_login"  value="">
                            <input id="otp_code" type="number" class="form-control" name="otp_code" value="{{ old('otp_code') }}"  placeholder="{{ __('Enter OTP') }}">
                        </div>
                        <button type="submit" class="otpCodeCheck">{{ __('Verify Account') }}</button>
                    </form>



                    <div class="resend-verify-code-wrap">
                        <span>{{ __('Did not you receive any code?') }}</span>
                        <strong>
                            <a class="text-center"
                               @if(empty($user_details))
                                   href="{{ route('user.resent.otp', $user_id) }}"
                               @else href="{{ route('user.resent.otp.login', $user_details->id) }}" @endif > {{ __('Resend Code') }}</a>
                        </strong>
                    </div>
                </div>
                <br>

            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @php
        if(empty($user_details)){
             $user_otp = \App\User::select('id', 'otp_expire_at')->findOrFail($user_id);
        }else{
             $user_otp = \App\User::select('id', 'otp_expire_at')->findOrFail($user_details->id);
        }

        $cookie_name = 'otp_expire_timestamp';
        $initial_expire_timestamp = null;

        if (!empty($user_otp->otp_expire_at)) {
            $current_cookie_value = request()->cookie($cookie_name);
            $db_expire_timestamp = \Carbon\Carbon::parse($user_otp->otp_expire_at, 'UTC')->timestamp;
            $initial_expire_timestamp = $db_expire_timestamp;

            if ($current_cookie_value === null || $db_expire_timestamp > $current_cookie_value) {
                cookie()->queue($cookie_name, $db_expire_timestamp, $db_expire_timestamp - time());
            }
        }
    @endphp

    <script type="text/javascript">
        "use strict";
        $(document).ready(function () {

            var counter = $("#counter");
            var cookieName = 'otp_expire_timestamp';
            var timer;

            // --- COOKIE FUNCTION ---
            function getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) {
                    return parts.pop().split(';').shift();
                }
                return null;
            }

            function startCountdown(initialSeconds) {
                if (timer) clearInterval(timer);
                var seconds = parseInt(initialSeconds, 10);
                function updateCounterDisplay() {
                    var minutes = Math.floor(seconds / 60);
                    var remainingSeconds = seconds % 60;
                    counter.text(
                        (minutes < 10 ? "0" : "") + minutes + ":" + (remainingSeconds < 10 ? "0" : "") + remainingSeconds
                    );
                }
                updateCounterDisplay();
                timer = setInterval(function () {
                    if (seconds > 0) {
                        seconds--;
                        updateCounterDisplay();
                    } else {
                        clearInterval(timer);
                        counter.text("{{ __('Expired') }}");
                        $('button[type="submit"]').prop('disabled', true).text('{{ __('Expired') }}');
                    }
                }, 1000);
            }

            // Get the initial timestamp from PHP
            var expireTimestamp = {{ $initial_expire_timestamp ?? 'null' }};

            // If PHP value is not set, fall back to the cookie
            if (!expireTimestamp) {
                expireTimestamp = getCookie(cookieName);
            }

            if (expireTimestamp) {
                var expireTimestampNum = parseInt(expireTimestamp, 10);
                var currentTimestamp = Math.floor(Date.now() / 1000);
                var remainingSeconds = expireTimestampNum - currentTimestamp;

                if (remainingSeconds > 0) {
                    startCountdown(remainingSeconds);
                } else {
                    counter.text("{{ __('Expired') }}");
                    eraseCookie(cookieName);
                }
            } else {
                counter.text("{{ __('00:00') }}");
            }

            function eraseCookie(name) {
                document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            }

            // --- FORM VALIDATION ---
            $(document).on('submit', '.signup-forms', function(e) {
                var otpCode = $("#otp_code").val();
                if (otpCode === ""){
                    toastr.error("{{__('OTP code is required')}}", "{{__('Warning')}}");
                    return false;
                }
            });

        });
    </script>
@endsection