<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>How To Create Web Notifications In Laravel 9 Using Pusher - Websolutionstuff</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

</head>
<body>
<h2>Name : <span id="name"></span></h2>
<h2>Language : <span id="lang"></span></h2>
<h2>Invigilator Name : <span id="invg"></span></h2>
<h2>Course : <span id="course"></span></h2>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="//js.pusher.com/3.1/pusher.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<script type="text/javascript">

    var pusher = new Pusher('797c52fa232a2f6149b8', {
        encrypted: true,
        cluster: 'ap2'
    });

    var channel = pusher.subscribe('examData');
    channel.bind('App\\Events\\CourseEvent', function(data) {
        console.log('My exam data',data.data.stdName);
        $('#name').text(data.data.stdName);
        $('#lang').text(data.data.qLang);
        $('#invg').text(data.data.ingName);
        $('#course').text(data.data.courseName);


    });
</script>
</body>
</html>
