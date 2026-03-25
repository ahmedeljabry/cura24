@extends('backend.admin-master')
@section('site-title')
    {{__('Seller Verification Template')}}
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
                            <h4 class="header-title">{{__('Seller Order Ticket Template')}}</h4>
                            <a class="btn btn-info" href="{{route('admin.email.template.all')}}">{{__('All Email Templates')}}</a>
                        </div>
                        <form action="{{route('admin.seller.verification')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content margin-top-30">
                                <div class="form-group">
                                    <label for="seller_verification_subject">{{__('Email Subject')}}</label>
                                    <input type="text" name="seller_verification_subject"  class="form-control" value="{{ get_static_option('seller_verification_subject') ?? __('Seller Verification Request') }}">
                                </div>
                                <div class="form-group">
                                    <label for="seller_verification_message">{{ __('Email Message') }}</label>
                                    <textarea id="seller-verification-message-editor" class="form-control" name="seller_verification_message">{!! get_static_option('seller_verification_message') ?? '' !!}</textarea>
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
            // Initialize Jodit for seller verification message editor
            let sellerVerificationMessageJodit = null;
            if ($('#seller-verification-message-editor').length && !$('#seller-verification-message-editor').hasClass('jodit-initialized')) {
                $('#seller-verification-message-editor').addClass('jodit-initialized');
                sellerVerificationMessageJodit = Jodit.make('#seller-verification-message-editor', {
                    height: 300,
                    placeholder: '{{ __("Type Seller Verification Message") }}',
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
