@extends('backend.admin-master')
@section('site-title')
    {{__('Buyer Report Template')}}
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
                            <h4 class="header-title">{{__('Buyer Report Template')}}</h4>
                            <a class="btn btn-info" href="{{route('admin.email.template.all')}}">{{__('All Email Templates')}}</a>
                        </div>
                        <form action="{{route('admin.buyer.report')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content margin-top-30">
                                <div class="form-group">
                                    <label for="service_approve_subject">{{__('Email Subject')}}</label>
                                    <input type="text" name="buyer_report_subject"  class="form-control" value="{{ get_static_option('buyer_report_subject') ?? __('Buyer New Report') }}">
                                </div>
                                <div class="form-group">
                                    <label for="buyer_report_message">{{ __('Email Message') }}</label>
                                    <textarea id="buyer-report-message-editor" class="form-control" name="buyer_report_message">{!! get_static_option('buyer_report_message') ?? '' !!}</textarea>
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
            // Initialize Jodit for buyer report message editor
            let buyerReportMessageJodit = null;
            if ($('#buyer-report-message-editor').length && !$('#buyer-report-message-editor').hasClass('jodit-initialized')) {
                $('#buyer-report-message-editor').addClass('jodit-initialized');
                buyerReportMessageJodit = Jodit.make('#buyer-report-message-editor', {
                    height: 300,
                    placeholder: '{{ __("Type Buyer Report Message") }}',
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
