@extends('backend.admin-master')
@section('site-title')
    {{__('Subscription Payment Status Update')}}
@endsection
@section('style')
    <x-media.css/>
    <link rel="stylesheet" href="{{asset('assets/backend/css/jodit.fat.min.css')}}">
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40"></div>
                <x-msg.success/>
                <x-msg.error/>
            </div>
            <div class="col-lg-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <div class="header-wrapp d-flex justify-content-between">
                            <h4 class="header-title">{{__('Subscription Payment Status Update')}}</h4>
                            <a class="btn btn-info" href="{{route('admin.email.template.all')}}">{{__('All Email Templates')}}</a>
                        </div>
                        <form action="{{route('admin.subscription.payment.status.email')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content margin-top-30">
                                <div class="form-group">
                                    <label for="payment_subscription_email_subject">{{__('Email Subject')}}</label>
                                    <input type="text" name="payment_subscription_email_subject"  class="form-control" value="{{ get_static_option('payment_subscription_email_subject') ?? __('Subscription Payment Status') }}">
                                </div>
                                <div class="form-group">
                                    <label for="payment_subscription_seller_message">{{ __('Email Message For Seller') }}</label>
                                    <textarea id="payment-subscription-seller-message-editor" class="form-control" name="payment_subscription_seller_message">{!! get_static_option('payment_subscription_seller_message') ?? '' !!}</textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <x-media.js />
    <script src="{{asset('assets/backend/js/jodit.fat.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            // Initialize Jodit for payment subscription seller message editor
            let paymentSubscriptionSellerMessageJodit = null;
            if ($('#payment-subscription-seller-message-editor').length && !$('#payment-subscription-seller-message-editor').hasClass('jodit-initialized')) {
                $('#payment-subscription-seller-message-editor').addClass('jodit-initialized');
                paymentSubscriptionSellerMessageJodit = Jodit.make('#payment-subscription-seller-message-editor', {
                    height: 300,
                    placeholder: '{{ __("Type Payment Subscription Message For Seller") }}',
                    buttons: [
                        'bold', 'italic', 'underline', '|',
                        'ul', 'ol', '|',
                        'outdent', 'indent', '|',
                        'font', 'fontsize', 'brush', 'paragraph', '|',
                        'align', 'undo', 'redo', '|',
                        'link', 'image', 'video', 'table', '|',
                        'hr', 'eraser', 'fullsize'
                    ],
                    uploader: {
                        insertImageAsBase64URI: true
                    }
                });
            }
        });
    </script>
@endsection
