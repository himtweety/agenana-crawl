window._ = require('lodash');

try {
    require('bootstrap');
} catch (e) { }

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.$ = require('jquery');

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

$(document).ready(function () {
    $('#ajaxSubmit').click(function (e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });

    });
});

function updateStatus(siteId) {
    $.ajax({
        method: "GET",
        url: "/site-status/1"
    })
        .done((response) => {
            console.log(response);
            $("#load-status").html(`We are processing records, ${response.completed} out of ${response.pending + response.completed} total pages are processed`);

            if (response.pending > 0) {
                setTimeout(function () {
                    updateStatus(siteId)
                }, 5000)
            }
        });
}
$(document).ready(function () {

    updateStatus("{{$site->id}}");
});