@extends('backend.admin-master')
@section('site-title')
    {{__('Job Apply Template')}}
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
                            <h4 class="header-title">{{__('Job Apply Template')}}</h4>
                            <a class="btn btn-info" href="{{route('admin.email.template.all')}}">{{__('All Email Templates')}}</a>
                        </div>
                        <form action="{{route('admin.job.apply.email')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content margin-top-30">
                                <div class="form-group">
                                    <label for="job_apply_subject">{{__('Email Subject')}}</label>
                                    <input type="text" name="job_apply_subject"  class="form-control" value="{{ get_static_option('job_apply_subject') ?? __('New Application Created') }}">
                                </div>
                                <div class="form-group">
                                    <label for="job_apply_message">{{ __('Email Message') }}</label>
                                    <textarea id="job-apply-message-editor" class="form-control" name="job_apply_message">{!! get_static_option('job_apply_message') ?? '' !!}</textarea>
                                </div>
                                    <small class="form-text text-muted text-danger"><code>@job_post_id</code> {{__('will be replaced by dynamically with job post id.')}}</small>
                                    <small class="form-text text-muted text-danger"><code>@seller_name</code> {{__('will be replaced by dynamically with seller name.')}}</small>
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
            // Initialize Jodit for job apply message editor
            let jobApplyMessageJodit = null;
            if ($('#job-apply-message-editor').length && !$('#job-apply-message-editor').hasClass('jodit-initialized')) {
                $('#job-apply-message-editor').addClass('jodit-initialized');
                jobApplyMessageJodit = Jodit.make('#job-apply-message-editor', {
                    height: 300,
                    placeholder: '{{ __("Type Job Apply Message") }}',
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
