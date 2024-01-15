<!DOCTYPE html>
<head>
    <title>RGMS Notifiy</title>
    <script src="https://code.jquery.com/jquery-3.6.3.slim.min.js" integrity="sha256-ZwqZIVdD3iXNyGHbSYdsmWP//UBokj2FHAxKuSBKDSo=" crossorigin="anonymous"></script>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('baca5e8ba2bea42ee5ca', {
            cluster: 'ap2'
        });

        var channel = pusher.subscribe('popup-channel');
        channel.bind('user-register', function(data) {
            alert(JSON.stringify(data));
        });
    </script>
</head>
<body>
<h1>Pusher RGMS</h1>

</body>
