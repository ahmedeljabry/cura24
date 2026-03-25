@extends('backend.admin-master')

@section('site-title')
    {{ __('WhatsApp Settings') }}
@endsection

@section('style')
    <style>
        .fs-26 {
            font-size: 26px;
            line-height: 1.5;
        }
    </style>
@endsection

@section('content')
    <div class="row g-4 mt-0">
        <div class="col-xl-10 col-lg-10 mx-auto">
            <div class="dashboard__card bg__white p-4 radius-10 shadow-sm">
                {{-- Header --}}
                <div class="row align-items-center mb-4">
                    <div class="col-md-6">
                        <h2 class="fs-26 fw-bold mb-0">{{ __('WhatsApp Settings') }}</h2>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <div class="d-flex flex-wrap justify-content-md-end g-2">
                            <a href="{{ route('admin.whatsapp.message.setting') }}" class="btn btn-sm btn-primary">
                                {{ __('Set Default Messages') }}
                            </a>
                            <a href="{{ route('admin.whatsapp.button-text.setting') }}" class="btn btn-sm btn-secondary">
                                {{ __('Set Button Text') }}
                            </a>
                            <a href="{{ route('admin.whatsapp.message.template.guide') }}" class="btn btn-sm btn-success">
                                {{ __('Rules of Template Create') }}
                            </a>
                        </div>
                    </div>
                </div>
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <x-validation.error/>
                {{-- Form --}}
                <form action="{{ route('admin.whatsapp.setting.update') }}" method="POST">
                    @csrf

                    {{-- Verify Token --}}
                    <div class="form-group mb-4">
                        <label for="whatsapp_verify_token" class="fw-semibold">{{ __('WhatsApp Verify Token') }}</label>
                        <input type="text"
                               id="whatsapp_verify_token"
                               name="whatsapp_verify_token"
                               class="form-control mt-2"
                               value="{{ isset($isDemoMiddlewareIsEnabled) ? __('Your whatsapp verify token...') : get_whatsapp_option('whatsapp_verify_token') }}"
                               readonly>
                        <small class="text-muted mt-1 d-block">
                            {{ __("This token is used during Webhook Verification in the Meta Developer Portal. Meta will compare this with your app's response.") }}
                        </small>
                    </div>

                    {{-- Phone Number ID --}}
                    <div class="form-group mb-4">
                        <label for="whatsapp_phone_number_id" class="fw-semibold">{{ __('WhatsApp Phone Number ID') }}</label>
                        <input type="text"
                               id="whatsapp_phone_number_id"
                               name="whatsapp_phone_number_id"
                               class="form-control mt-2"
                               value="{{ isset($isDemoMiddlewareIsEnabled) ? 'Your whatsapp phone number id...' : get_whatsapp_option('whatsapp_phone_number_id') }}"
                                {{ isset($isDemoMiddlewareIsEnabled) ? 'readonly' : '' }}>
                        <small class="text-muted mt-1 d-block">
                            {{ __("You can find this in your Meta Business Manager under WhatsApp assets.") }}
                        </small>
                    </div>

                    {{-- Permanent Token --}}
                    <div class="form-group mb-4">
                        <label for="whatsapp_permanent_token" class="fw-semibold">{{ __('WhatsApp Permanent Token') }}</label>
                        <input type="text"
                               id="whatsapp_permanent_token"
                               name="whatsapp_permanent_token"
                               class="form-control mt-2"
                               value="{{ isset($isDemoMiddlewareIsEnabled) ? 'Your whatsapp permanent token...' : get_whatsapp_option('whatsapp_permanent_token') }}"
                                {{ isset($isDemoMiddlewareIsEnabled) ? 'readonly' : '' }}>
                        <small class="text-muted mt-1 d-block">
                            {{ __("This long-lived access token is tied to your Meta system user and used for authenticating API requests.") }}
                        </small>
                    </div>

                    {{-- Submit Button --}}
                    <div class="text-start mt-4">
                        <button type="submit" class="btn btn-primary px-4">{{ __('Update Settings') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function ($) {
            "use strict";
            $(document).ready(function () {
                // Additional JS here if needed
            });
        })(jQuery);
    </script>
@endsection
