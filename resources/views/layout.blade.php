<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Agency Analytics Demo</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="container">
        @yield('content')
    </div>
    <script src="{{ asset('js/app.js') }}" type="text/js"></script>
    <script src="http://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous">
    </script>
    <script>
        jQuery(document).ready(function() {
            jQuery('#ajaxSubmit').click(function(e) {
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
                    $("#load-status").html(`${response.completed} out of ${response.pending + response.completed} total links are processed`);

                    if (response.pending > 0) {
                        setTimeout(function() {
                            updateStatus(siteId)
                        }, 5000)
                    }
                });
        }
    </script>
</body>

</html>