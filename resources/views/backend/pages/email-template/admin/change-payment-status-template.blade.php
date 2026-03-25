@extends('backend.admin-master')
@section('site-title')
    {{__('Payment Status Change Template')}}
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
                            <h4 class="header-title">{{__('Payment Status Change Template')}}</h4>
                            <a class="btn btn-info" href="{{route('admin.email.template.all')}}">{{__('All Email Templates')}}</a>
                        </div>
                        <form action="{{route('admin.payment.status.change.email')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content margin-top-30">
                                <div class="form-group">
                                    <label for="admin_change_payment_status_subject">{{__('Email Subject')}}</label>
                                    <input type="text" name="admin_change_payment_status_subject"  class="form-control" value="{{ get_static_option('admin_change_payment_status_subject') ?? __('Payment Status Changed') }}">
                                </div>
                                <div class="form-group">
                                    <label for="admin_change_payment_status_message">{{ __('Email Message') }}</label>
                                    <textarea id="admin-change-payment-status-message-editor" class="form-control" name="admin_change_payment_status_message">{{ get_static_option('admin_change_payment_status_message') ?? '' }}</textarea>
                                </div>
                                    <small class="form-text text-muted text-danger margin-top-20"><code>@name</code> {{__('will be replaced by dynamically with  name.')}}</small>
                                    <small class="form-text text-muted text-danger"><code>@old_status</code> {{__('will be replaced by dynamically with  old status.')}}</small>
                                    <small class="form-text text-muted text-danger"><code>@new_status</code> {{__('will be replaced by dynamically new status.')}}</small>
                                    <small class="form-text text-muted text-danger"><code>@order_id</code> {{__('will be replaced by dynamically with order id.')}}</small>
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
            // Initialize Jodit for admin change payment status message editor
            let adminChangePaymentStatusMessageJodit = null;
            if ($('#admin-change-payment-status-message-editor').length && !$('#admin-change-payment-status-message-editor').hasClass('jodit-initialized')) {
                $('#admin-change-payment-status-message-editor').addClass('jodit-initialized');
                adminChangePaymentStatusMessageJodit = Jodit.make('#admin-change-payment-status-message-editor', {
                    height: 300,
                    placeholder: '{{ __("Type Admin Payment Status Message") }}',
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
