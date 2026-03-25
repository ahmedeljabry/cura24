@extends('frontend.user.buyer.buyer-master')
@section('site-title')
    {{ __('Live Chat') }}
@endsection

@section('style')
  <style>
      /*new theme chat css*/
      .base_sent.msg_container {
          display: flex;
          align-items: flex-start;
          gap: 10px;
          justify-content: flex-end;
          text-align: right;
      }

      .conversation-message-contents {
          flex-grow: 1;
      }

      .conversation-bg-thumb {
          max-width: 40px;
          border-radius: 50%;
      }

      .conversation-bg-thumb img {
          border-radius: 50%;
      }

      .conversation-message-contents > p {}

      .conversation-message-contents > p {
          background-color: #1dbf7329;
          padding: 10px 20px;
          border-radius: 0 10px 10px 10px;
          color: #000;
          font-size: 16px;
          font-weight: 500;
          display: inline-block;
      }

      .conversation-message-contents p {
          background-color: #F5F6F9;
          padding: 10px 20px;
          border-radius: 10px 0px 10px 10px;
          color: #000;
          font-size: 16px;
          font-weight: 500;
          display: table;
          margin-left: auto;
      }

      .conversation-message-contents time {
          font-size: 16px;
          color: var(--paragraph-color);
          font-weight: 500;
          display: block;
          font-size: 12px;
      }
      .conversation-message-contents .msg_sent img {
          max-width: 250px;
          background-color: #F5F6F9;
          padding: 10px 20px;
          border-radius: 10px 0px 10px 10px;
          color: #000;
          font-size: 16px;
          font-weight: 500;
          display: table;
          margin-left: auto;
      }

      .unread-count-badge {
          position: absolute;
          top: 6px;
          right: 6px;
          background-color: red;
          color: white;
          font-size: 10px;
          font-weight: bold;
          padding: 2px 5px;
          border-radius: 12px;
          min-width: 18px;
          text-align: center;
          line-height: 1;
      }

      .unread-count-badge.hidden {
          display: none !important;
      }
  </style>
@endsection
@section('content')
<x-frontend.seller-buyer-preloader/>
@include('frontend.user.buyer.partials.sidebar-two')
<div class="dashboard__right">
    @include('frontend.user.buyer.header.buyer-header')
    <div class="dashboard__body">
        <div class="dashboard__inner">
            <div class="chat_wrapper">
                <div class="chat_wrapper__flex" id="app">
                    <div class="chat_sidebar d-lg-none">
                        <i class="fas fa-bars"></i>
                    </div>
                    <div class="chat_wrapper__contact">
                        <div class="chat_wrapper__contact__close">
                            <!-- Chat section start -->
                            <div id="app">
                                <input type="hidden" id="current_user" value="{{ \Auth::user()->id }}" />
                                <h5 class="panel-title mb-3">{{__('All Contacts')}}</h5>
                                @if($users->count() > 0)
                                    <ul class="chat_wrapper__contact__list" id="users">    
                                    @foreach($users as $user)
                                    <li class="chat_wrapper__contact__list__item chat_item">
                                        <div class="chat_wrapper__contact__list__flex">
                                            <div class="chat_wrapper__contact__list__thumb">
                                                {!! render_image_markup_by_attachment_id($user->image) !!}
                                                @if(\Illuminate\Support\Facades\Cache::has('user-is-online-' . $user->id))
                                                    <div class="notification__dots active"></div>
                                                @else
                                                    <div class="notification__dots"></div>
                                                @endif
                                                @php
                                                    $userId = $user->id;
                                                    $unreadMessages = \Modules\LiveChat\Entities\LiveChatMessage::where('seller_id', $user->id)
                                                        ->where('buyer_id', \Auth::guard('web')->user()->id)
                                                        ->where('from_user', '!=', \Auth::guard('web')->user()->id)
                                                        ->where('is_read', false)
                                                        ->count();
                                                @endphp

                                                <div class="unread-count-badge {{ $unreadMessages == 0 ? 'hidden' : '' }}"
                                                     id="buyer-unread-count-badge-{{ $userId }}">
                                                    {{ $unreadMessages > 9 ? '9+' : $unreadMessages }}
                                                </div>
                                             
                                            </div>
                                            <div class="chat_wrapper__contact__list__contents">
                                                <div class="chat_wrapper__contact__list__contents__details">
                                                    <h4 class="chat_wrapper__contact__list__contents__title">
                                                        @php $user_image = get_attachment_image_by_id($user->image); @endphp
                                                        <a href="javascript:void(0)" class="chat-toggle"
                                                           data-id="{{ $user->id }}"
                                                           data-user="{{ $user->name }}"
                                                           data-user_image="{{ !empty($user_image) ? $user_image['img_url'] : asset('assets/frontend/img/user-no-image.png') }}"
                                                           data-user-status="{{ \Illuminate\Support\Facades\Cache::has('user-is-online-' . $user->id) ? __('Active Now') : __('Offline') }}"
                                                        >{{ $user->name }}</a>
                                                    </h4>
                                                    <p class="chat_wrapper__contact__list__contents__para">
                                                        @if(\Illuminate\Support\Facades\Cache::has('user-is-online-' . $user->id))
                                                            <span class="text-success">{{ __('Online') }}</span>
                                                        @else
                                                            <span class="text-secondary">{{ __('Offline') }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                                <span class="chat_wrapper__contact__list__time">
                                                     @if(\Illuminate\Support\Facades\Cache::has('user-is-online-' . $user->id))@else
                                                        {{ \Carbon\Carbon::parse($user->last_seen)->diffForHumans() }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </li>   
                                    @endforeach
                                </ul>
                                @else
                                    <div class="chat_wrapper__details__inner__chat__contents">
                                        <p class="no_data_found_for_buyer_seller_panel">
                                            {{ __('No Contacts Yet') }}
                                        </div>
                                @endif
                                <!-- Chat section End -->  
                            </div>   
                        </div>       
                    </div>
                    <div id="chat-overlay" class="chat_wrapper__details"></div>
                    @include('livechat::frontend.buyer.partials.chat-box-two')
                </div>
            </div>
        </div>
    </div>    
@endsection
@section('scripts')
{{--    @if(moduleExists("LiveChat"))--}}
{{--         <script>--}}
{{--            window.PUSHER_CONFIG = {--}}
{{--                key: "{{ get_static_option('pusher_app_key') }}",--}}
{{--                cluster: "{{ get_static_option('pusher_app_cluster') }}"--}}
{{--            };--}}
{{--        </script>--}}
{{--        <x-livechat.widget-js />--}}
{{--    @endif--}}

    <script>
        $(document).ready(function(){
            $(document).on('change', '.new_image_add', function (event) {
                $(this).next('#uploadImage').html(event.target.files[0].name);
            });

            $(document).on('click', '.chat_send_message_paper_button_new_design', function (event) {
                setTimeout(function() {
                    $(".new_image_add").val(''); // clear image input value
                    $("#uploadImage").text("{{ __('Attach Files') }}"); // file name remove
                }, 1000);
            });

            $(document).on('change', '.new_image_add', function (event) {
                let fileName = event.target.files[0]?.name ?? '';
                if (fileName) {
                    $('#uploadImage').html('<i class="fa-solid fa-paperclip"></i> ' + fileName);
                    $('#removeAttachedFile').show();
                }
            });

            $(document).on('click', '#removeAttachedFile', function () {
                let $fileInput = $('.new_image_add');
                $fileInput.val('');
                $('#uploadImage').html('<i class="fa-solid fa-paperclip"></i> {{ __('Attach Files') }}');
                $('#removeAttachedFile').hide();
            });

            $(document).on('click', '.chat_send_message_paper_button_new_design', function () {
                setTimeout(function () {
                    $(".new_image_add").val('');
                    $("#uploadImage").html('<i class="fa-solid fa-paperclip"></i> {{ __('Attach Files') }}');
                    $("#removeAttachedFile").hide();
                }, 1000);
            });

            // Mark messages as read when chat is opened
            $(document).on('click', '.chat-toggle', function() {
                let userId = $(this).data('id');
                let contactItem = $(this).closest('.chat_wrapper__contact__list__item');
                $.ajax({
                    url: '{{ route("buyer.chat.mark.as.read") }}',
                    method: 'POST',
                    data: {
                        seller_id: userId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.state === 1) {
                            let $badge = contactItem.find('.unread-count-badge');
                            if ($badge.length === 0) {
                                // Create badge if missing
                                $badge = $(`<div class="unread-count-badge hidden" id="buyer-unread-count-badge-${userId}">0</div>`);
                                contactItem.append($badge);
                            } else {
                                $badge.text('0').addClass('hidden');
                            }
                             $.ajax({
                                url: '{{ route("buyer.chat.unread.count") }}',
                                method: 'GET',
                                data: {_token: '{{ csrf_token() }}'},
                                success: function (data) {
                                    if (data.status === 'success') {
                                        let count = data.totalUnreadMessages;
                                        let badge = $('#chat-unread-count'); // Use ID for specificity
                                        // Update badge text (0, 1-9, or 9+)
                                        badge.text(count > 9 ? '9+' : count);
                                        $('.dashboard__header__chat').show();
                                    }
                                },
                                error: function (xhr) {
                                    console.error('Failed to fetch unread count:', xhr);
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to mark messages as read:', xhr);
                    }
                });
            });
        });
    </script>
@endsection