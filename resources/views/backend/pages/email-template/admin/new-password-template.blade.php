@extends('backend.admin-master')
@section('site-title')
    {{__('New Password Template')}}
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
                            <h4 class="header-title">{{__('User New Password Template')}}</h4>
                            <a class="btn btn-info" href="{{route('admin.email.template.all')}}">{{__('All Email Templates')}}</a>
                        </div>
                        <form action="{{route('admin.user.new.password.email')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content margin-top-30">
                                <div class="form-group">
                                    <label for="admin_user_new_password_subject">{{__('Email Subject')}}</label>
                                    <input type="text" name="admin_user_new_password_subject"  class="form-control" value="{{ get_static_option('admin_user_new_password_subject') ?? __('Password Change Success') }}">
                                </div>
                                <div class="form-group">
                                    <label for="admin_user_new_password_message">{{ __('Email Message') }}</label>
                                    <textarea id="admin-user-new-password-message-editor" class="form-control" name="admin_user_new_password_message">{{ get_static_option('admin_user_new_password_message') ?? '' }}</textarea>
                                </div>
                                <small class="form-text text-muted text-danger margin-top-20"><code>@new_password</code> {{__('will be replaced by dynamically with new password.')}}</small>
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
            // Initialize Jodit for admin user new password message editor
            let adminUserNewPasswordMessageJodit = null;
            if ($('#admin-user-new-password-message-editor').length && !$('#admin-user-new-password-message-editor').hasClass('jodit-initialized')) {
                $('#admin-user-new-password-message-editor').addClass('jodit-initialized');
                adminUserNewPasswordMessageJodit = Jodit.make('#admin-user-new-password-message-editor', {
                    height: 300,
                    placeholder: '{{ __("Type Admin User New Password Message") }}',
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
