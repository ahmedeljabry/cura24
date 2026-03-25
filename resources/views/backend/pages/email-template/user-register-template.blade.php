@extends('backend.admin-master')
@section('site-title')
    {{__('User Register Email Template')}}
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
                            <h4 class="header-title">{{__('User Register Email Template')}}</h4>
                            <a class="btn btn-info" href="{{route('admin.email.template.all')}}">{{__('All Email Templates')}}</a>
                        </div>
                        <form action="{{route('admin.email.user.register.template')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content margin-top-30">
                                <div class="form-group">
                                    <label for="user_register_subject">{{__('Email Subject')}}</label>
                                    <input type="text" name="user_register_subject"  class="form-control" value="{{ get_static_option('user_register_subject') ?? 'New User Registration' }}">
                                </div>
                                <div class="form-group">
                                    <label for="user_register_message">{{ __('Email Message') }}</label>
                                    <textarea id="user-register-message-editor" class="form-control" name="user_register_message">{!! get_static_option('user_register_message') ?? '<p>Hello @name,</p></p>You have user registered as a @type Username: @username Email: @email</p>' !!}</textarea>
                                </div>
                                    <small class="form-text text-muted text-danger margin-top-20"><code>@name</code> {{__('will be replaced by dynamically with  name.')}}</small>
                                    <small class="form-text text-muted text-danger"><code>@type</code> {{__('will be replaced by dynamically with  user type.')}}</small>
                                    <small class="form-text text-muted text-danger"><code>@username</code> {{__('will be replaced by dynamically with username.')}}</small>
                                    <small class="form-text text-muted text-danger"><code>@email</code> {{__('will be replaced by dynamically with email.')}}</small>
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
            // Initialize Jodit for user register message editor
            let userRegisterMessageJodit = null;
            if ($('#user-register-message-editor').length && !$('#user-register-message-editor').hasClass('jodit-initialized')) {
                $('#user-register-message-editor').addClass('jodit-initialized');
                userRegisterMessageJodit = Jodit.make('#user-register-message-editor', {
                    height: 300,
                    placeholder: '{{ __("Type Email Message") }}',
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
