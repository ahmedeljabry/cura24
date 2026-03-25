@extends('backend.admin-master')
@section('site-title')
    {{__('New Order Template')}}
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
                            <h4 class="header-title">{{__('New Order Template')}}</h4>
                            <a class="btn btn-info" href="{{route('admin.email.template.all')}}">{{__('All Email Templates')}}</a>
                        </div>
                        <form action="{{route('admin.new.order.ad.sell.buyer.email')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content margin-top-30">
                                <div class="form-group">
                                    <label for="new_order_email_subject">{{__('Email Subject')}}</label>
                                    <input type="text" name="new_order_email_subject"  class="form-control" value="{{ get_static_option('new_order_email_subject') ?? __('New Order') }}">
                                </div>
                                <div class="form-group">
                                    <label for="new_order_buyer_message">{{ __('Email Message For Buyer') }}</label>
                                    <textarea id="new-order-buyer-message-editor" class="form-control" name="new_order_buyer_message">{!! get_static_option('new_order_buyer_message') ?? '' !!}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="new_order_admin_seller_message">{{ __('Email Message For Seller And Admin') }}</label>
                                    <textarea id="new-order-admin-seller-message-editor" class="form-control" name="new_order_admin_seller_message">{!! get_static_option('new_order_admin_seller_message') ?? '' !!}</textarea>
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
            // Initialize Jodit for new order buyer message editor
            let newOrderBuyerMessageJodit = null;
            if ($('#new-order-buyer-message-editor').length && !$('#new-order-buyer-message-editor').hasClass('jodit-initialized')) {
                $('#new-order-buyer-message-editor').addClass('jodit-initialized');
                newOrderBuyerMessageJodit = Jodit.make('#new-order-buyer-message-editor', {
                    height: 300,
                    placeholder: '{{ __("Type New Order Message For Buyer") }}',
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

            // Initialize Jodit for new order admin seller message editor
            let newOrderAdminSellerMessageJodit = null;
            if ($('#new-order-admin-seller-message-editor').length && !$('#new-order-admin-seller-message-editor').hasClass('jodit-initialized')) {
                $('#new-order-admin-seller-message-editor').addClass('jodit-initialized');
                newOrderAdminSellerMessageJodit = Jodit.make('#new-order-admin-seller-message-editor', {
                    height: 300,
                    placeholder: '{{ __("Type New Order Message For Seller And Admin") }}',
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
