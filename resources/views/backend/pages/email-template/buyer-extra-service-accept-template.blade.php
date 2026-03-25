@extends('backend.admin-master')
@section('site-title')
    {{__('Buyer Extra Service Accept Template')}}
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
                            <h4 class="header-title">{{__('Buyer Extra Service Accept Template')}}</h4>
                            <a class="btn btn-info" href="{{route('admin.email.template.all')}}">{{__('All Email Templates')}}</a>
                        </div>
                        <form action="{{route('admin.buyer.extra.service.accept')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content margin-top-30">
                                <div class="form-group">
                                    <label for="buyer_extra_service_subject">{{__('Email Subject')}}</label>
                                    <input type="text" name="buyer_extra_service_subject"  class="form-control" value="{{ get_static_option('buyer_extra_service_subject') ?? __('Extra Service Accepted') }}">
                                </div>
                                <div class="form-group">
                                    <label for="buyer_extra_service_message">{{ __('Email Message For Buyer') }}</label>
                                    <textarea id="buyer-extra-service-message-editor" class="form-control" name="buyer_extra_service_message">{!! get_static_option('buyer_extra_service_message') ?? '' !!}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="buyer_to_seller_extra_service_message">{{ __('Email Message For Seller') }}</label>
                                    <textarea id="buyer-to-seller-extra-service-message-editor" class="form-control" name="buyer_to_seller_extra_service_message">{!! get_static_option('buyer_to_seller_extra_service_message') ?? '' !!}</textarea>
                                </div>
                                <small class="form-text text-muted text-danger"><code>@order_id</code> {{__('will be replaced by dynamically with order id.')}}</small>
                                <small class="form-text text-muted text-danger"><code>@seller_name</code> {{__('will be replaced by dynamically with seller name.')}}</small>
                                <small class="form-text text-muted text-danger"><code>@buyer_name</code> {{__('will be replaced by dynamically with buyer name.')}}</small>
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
            // Initialize Jodit for buyer extra service message editor
            let buyerExtraServiceMessageJodit = null;
            if ($('#buyer-extra-service-message-editor').length && !$('#buyer-extra-service-message-editor').hasClass('jodit-initialized')) {
                $('#buyer-extra-service-message-editor').addClass('jodit-initialized');
                buyerExtraServiceMessageJodit = Jodit.make('#buyer-extra-service-message-editor', {
                    height: 300,
                    placeholder: '{{ __("Type Extra Service Message For Buyer") }}',
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

            // Initialize Jodit for buyer to seller extra service message editor
            let buyerToSellerExtraServiceMessageJodit = null;
            if ($('#buyer-to-seller-extra-service-message-editor').length && !$('#buyer-to-seller-extra-service-message-editor').hasClass('jodit-initialized')) {
                $('#buyer-to-seller-extra-service-message-editor').addClass('jodit-initialized');
                buyerToSellerExtraServiceMessageJodit = Jodit.make('#buyer-to-seller-extra-service-message-editor', {
                    height: 300,
                    placeholder: '{{ __("Type Extra Service Message For Seller") }}',
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
