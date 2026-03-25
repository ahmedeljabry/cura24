@include('frontend.user.seller.partials.header-two')
<!--Dashboard Markup -->
<div class="body-overlay"></div>
<div class="dashboard__area @if(Auth::guard('web')->user()->user_type == 0) seller_look @endif">
    <div class="container-fluid p-0">
        <div class="dashboard__contents__wrapper">
            <div class="dashboard__icon">
                <div class="dashboard__icon__bars sidebar-icon">
                    <i class="fa-solid fa-bars"></i>
                </div>
            </div>
            @auth
                <input type="hidden" id="current_user" value="{{ \Auth::user()->id }}" />
            @endauth
             @yield('content')
        </div>
    </div>
</div>
@include('frontend.user.seller.partials.footer-two')

@stack('scripts')
@once
    @if(moduleExists("LiveChat"))
        <script>
            // Get the broadcast driver from admin settings or default to .env
            const broadcastDriver = "{{ get_static_option('broadcaster_option', env('BROADCAST_DRIVER', 'pusher')) }}";

            if (broadcastDriver === 'pusher') {
                window.PUSHER_CONFIG = {
                    key: "{{ get_static_option('pusher_app_key', env('PUSHER_APP_KEY')) }}",
                    cluster: "{{ get_static_option('pusher_app_cluster', env('PUSHER_APP_CLUSTER')) }}"
                };
            } else if (broadcastDriver === 'reverb') {
                window.REVERB_CONFIG = {
                    key: "{{ env('REVERB_APP_KEY') }}",
                    host: "{{ env('REVERB_HOST') }}",
                    port: "{{ env('REVERB_PORT') }}",
                    scheme: "{{ env('REVERB_SCHEME') }}"
                };
            }

            window.BROADCAST_DRIVER = broadcastDriver;
        </script>
        <x-livechat.widget-js/>
    @endif
@endonce