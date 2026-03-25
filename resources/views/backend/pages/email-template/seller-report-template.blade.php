@extends('backend.admin-master')
@section('site-title')
    {{__('Seller Report Template')}}
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
                            <h4 class="header-title">{{__('Seller Report Template')}}</h4>
                            <a class="btn btn-info" href="{{route('admin.email.template.all')}}">{{__('All Email Templates')}}</a>
                        </div>
                        <form action="{{route('admin.seller.report')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content margin-top-30">
                                <div class="form-group">
                                    <label for="service_approve_subject">{{__('Email Subject')}}</label>
                                    <input type="text" name="seller_report_subject"  class="form-control" value="{{ get_static_option('seller_report_subject') ?? __('Seller New Report') }}">
                                </div>
                                <div class="form-group">
                                    <label for="seller_report_message">{{ __('Email Message') }}</label>
                                    <textarea id="seller-report-message-editor" class="form-control" name="seller_report_message">{!! get_static_option('seller_report_message') ?? '' !!}</textarea>
                                </div>
                                    <small class="form-text text-muted text-danger"><code>@report_id</code> {{__('will be replaced by dynamically with report id.')}}</small>
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
            // Initialize Jodit for seller report message editor
            let sellerReportMessageJodit = null;
            if ($('#seller-report-message-editor').length && !$('#seller-report-message-editor').hasClass('jodit-initialized')) {
                $('#seller-report-message-editor').addClass('jodit-initialized');
                sellerReportMessageJodit = Jodit.make('#seller-report-message-editor', {
                    height: 300,
                    placeholder: '{{ __("Type Seller Report Message") }}',
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
