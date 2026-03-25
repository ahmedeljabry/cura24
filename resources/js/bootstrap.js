window._ = require('lodash');
/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

// try {
//     window.Popper = require('popper.js').default;
//     window.$ = window.jQuery = require('jquery');

//     require('bootstrap');
// } catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

// Initialize Echo based on the broadcast driver
if (window.BROADCAST_DRIVER === 'reverb' && window.REVERB_CONFIG) {
    window.Pusher = require('pusher-js');

    const config = {
        broadcaster: 'reverb',
        key: window.REVERB_CONFIG.key,
        wsHost: window.REVERB_CONFIG.host,
        wsPort: window.REVERB_CONFIG.port,
        wssPort: window.REVERB_CONFIG.port,
        forceTLS: true, // Disable TLS
        enabledTransports: ['ws', 'wss'],
        authEndpoint: window.base_url + '/broadcasting/auth'
    };

    window.Echo = new Echo(config);

} else if (window.BROADCAST_DRIVER === 'pusher' && window.PUSHER_CONFIG) {
    window.Pusher = require('pusher-js');

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: window.PUSHER_CONFIG.key,
        cluster: window.PUSHER_CONFIG.cluster,
        forceTLS: true,
        authEndpoint: window.base_url + '/broadcasting/auth'
    });
} else {
    // console.error('No valid broadcast configuration found', {
    //     BROADCAST_DRIVER: window.BROADCAST_DRIVER,
    //     REVERB_CONFIG: window.REVERB_CONFIG,
    //     PUSHER_CONFIG: window.PUSHER_CONFIG
    // });
}