@extends('backend.admin-master')
@section('site-title')
    {{__('Service Approve Template')}}
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
                            <h4 class="header-title">{{__('Service Approve Template')}}</h4>
                            <a class="btn btn-info" href="{{route('admin.email.template.all')}}">{{__('All Email Templates')}}</a>
                        </div>
                        <form action="{{route('admin.service.approve.email')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content margin-top-30">
                                <div class="form-group">
                                    <label for="admin_service_approve_subject">{{__('Email Subject')}}</label>
                                    <input type="text" name="admin_service_approve_subject"  class="form-control" value="{{ get_static_option('admin_service_approve_subject') ?? __('Service Approve Success') }}">
                                </div>
                                <div class="form-group">
                                    <label for="admin_service_approve_message">{{ __('Email Message') }}</label>
                                    <textarea id="admin-service-approve-message-editor" class="form-control" name="admin_service_approve_message">{{ get_static_option('admin_service_approve_message') ?? '' }}</textarea>
                                </div>
                                    <small class="form-text text-muted text-danger margin-top-20"><code>@name</code> {{__('will be replaced by dynamically with  name.')}}</small>
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
            // Initialize Jodit for admin service approve message editor
            let adminServiceApproveMessageJodit = null;
            if ($('#admin-service-approve-message-editor').length && !$('#admin-service-approve-message-editor').hasClass('jodit-initialized')) {
                $('#admin-service-approve-message-editor').addClass('jodit-initialized');
                adminServiceApproveMessageJodit = Jodit.make('#admin-service-approve-message-editor', {
                    height: 300,
                    placeholder: '{{ __("Type Admin Service Approval Message") }}',
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
