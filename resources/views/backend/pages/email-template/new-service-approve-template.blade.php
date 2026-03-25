@extends('backend.admin-master')
@section('site-title')
    {{__('New Service Approve Template')}}
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
                            <h4 class="header-title">{{__('New Service Approve Template')}}</h4>
                            <a class="btn btn-info" href="{{route('admin.email.template.all')}}">{{__('All Email Templates')}}</a>
                        </div>
                        <form action="{{route('admin.seller.service.approve')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content margin-top-30">
                                <div class="form-group">
                                    <label for="service_approve_subject">{{__('Email Subject')}}</label>
                                    <input type="text" name="service_approve_subject"  class="form-control" value="{{ get_static_option('service_approve_subject') ?? __('New Service Approve Request') }}">
                                </div>
                                <div class="form-group">
                                    <label for="service_approve_message">{{ __('Email Message') }}</label>
                                    <textarea id="service-approve-message-editor" class="form-control" name="service_approve_message">{!! get_static_option('service_approve_message') ?? '' !!}</textarea>
                                </div>
                                    <small class="form-text text-muted text-danger"><code>@service_id</code> {{__('will be replaced by dynamically with service id.')}}</small>
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
            // Initialize Jodit for service approve message editor
            let serviceApproveMessageJodit = null;
            if ($('#service-approve-message-editor').length && !$('#service-approve-message-editor').hasClass('jodit-initialized')) {
                $('#service-approve-message-editor').addClass('jodit-initialized');
                serviceApproveMessageJodit = Jodit.make('#service-approve-message-editor', {
                    height: 300,
                    placeholder: '{{ __("Type Service Approval Message") }}',
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
