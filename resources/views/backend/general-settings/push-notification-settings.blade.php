@extends('backend.admin-master')
@section('site-title')
    {{__('Push Notification Settings')}}
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-12 mt-5">
                <x-msg.success/>
                <x-msg.error/>
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-4">{{__("Firebase Settings")}}</h4>
                        @if($firebaseFileExists)
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i> {{ __('Firebase JSON file is already uploaded.') }}
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-circle"></i> {{ __('No Firebase JSON file uploaded yet. Please upload a new one.') }}
                            </div>
                        @endif
                        <form action="{{route('admin.general.global.push.notification.settings')}}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="firebase_json"><strong>{{__('Upload Firebase JSON')}} <span class="text-danger">*</span></strong></label>
                                <input type="file" class="form-control" name="firebase_json" accept=".json">
                                <span class="form-text text-muted">{{__('Upload your Firebase service account JSON file for push notifications.')}}</span>
                            </div>


                            <button type="submit" id="update" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update Changes')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        (function($){
            "use strict";
            $(document).ready(function (e) {

                // remove input space
                $('.space_remove').keypress(function( e ) {
                    if(e.which === 32)
                        return false;
                });

                // remove copy past text space
                $('.space_remove').on('paste', function(e) {
                    var inputElement = this;
                    setTimeout(function() {
                        var pastedText = $(inputElement).val();
                        var cleanedText = pastedText.replace(/\s+/g, '');
                        $(inputElement).val(cleanedText);
                    }, 0);
                });


            })
        })(jQuery);
    </script>
@endsection