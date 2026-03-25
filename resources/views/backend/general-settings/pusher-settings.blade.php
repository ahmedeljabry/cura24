@extends('backend.admin-master')

@section('style')
    <x-media.css/>
    <link rel="stylesheet" href="{{asset('assets/backend/css/summernote-bs4.css')}}">
    <style>
        .pusher-fields { display: none; }
        .pusher-fields .form-control {
            width: 100%; /* Ensure full width for input fields */
            max-width: none; /* Override any max-width restrictions */
        }
        .pusher-fields .form-group {
            margin-bottom: 1rem; /* Consistent spacing */
        }
    </style>
@endsection

@section('site-title')
    {{__('Live Chat Settings')}}
@endsection

@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-12 mt-5">
                @include('backend.partials.message')
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__("Live Chat Settings")}}</h4>
                        @include('backend/partials/error')
                        <form action="{{route('admin.general.global.pusher.settings')}}" method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="broadcaster_option">{{__('Broadcaster Option')}}</label>
                                        <select name="broadcaster_option" id="broadcaster_option" class="form-control">
                                            <option value="pusher" {{ $selectedBroadcaster === 'pusher' ? 'selected' : '' }}>{{__('Pusher')}}</option>
                                            <option value="reverb" {{ $selectedBroadcaster === 'reverb' ? 'selected' : '' }}>{{__('Reverb')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="pusher-fields" style="{{ get_static_option('broadcaster_option') === 'pusher' ? 'display: block;' : 'display: none;' }}">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="pusher_app_id">{{__('Pusher App ID')}}</label>
                                                    <input type="text" name="pusher_app_id" id="pusher_app_id" value="{{get_static_option('pusher_app_id')}}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="pusher_app_key">{{__('Pusher App Key')}}</label>
                                                    <input type="text" name="pusher_app_key" id="pusher_app_key" value="{{get_static_option('pusher_app_key')}}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="pusher_app_secret">{{__('Pusher App Secret')}}</label>
                                                    <input type="text" name="pusher_app_secret" id="pusher_app_secret" value="{{get_static_option('pusher_app_secret')}}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="pusher_app_cluster">{{__('Pusher App Cluster')}}</label>
                                                    <input type="text" name="pusher_app_cluster" id="pusher_app_cluster" value="{{get_static_option('pusher_app_cluster')}}" class="form-control">
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update Changes')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-media.markup/>
@endsection
@section('script')
    <x-media.js/>
    <script src="{{asset('assets/backend/js/summernote-bs4.js')}}"></script>
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){
                <x-icon-picker/>
                <x-btn.update/>

                $('.summernote').summernote({
                    height: 150,   //set editable area's height
                    codemirror: { // codemirror options
                        theme: 'monokai'
                    }
                });

                function togglePusherFields() {
                    const broadcaster = $('#broadcaster_option').val();
                    $('.pusher-fields').toggle(broadcaster === 'pusher');
                }

                // Initial toggle on page load
                togglePusherFields();

                // Toggle on change
                $('#broadcaster_option').on('change', togglePusherFields);
            });
        }(jQuery));
    </script>
@endsection
