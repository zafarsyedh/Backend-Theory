<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Pusher</title>
</head>
<body>
<div class="container mt-5">
<form method="post" action="{{route('exam')}}">
    @csrf
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="inputEmail4">Student Name</label>
            <input type="text" class="form-control" id="inputEmail4" placeholder="Student Name" required name="std_name">
        </div>
        <div class="form-group col-md-6">
            <label for="inputState">Language</label>
            <select id="inputState" class="form-control" required name="lang">
                <option selected>Choose...</option>
                <option value="English">English</option>
                <option value="Urdu">Urdu</option>
            </select>
        </div>

    </div>
    <div class="form-group">
        <label for="inputAddress">Invigilator Name</label>
        <select id="inputState" class="form-control" name="invg_name" required>
            <option selected>Choose One</option>
            <option value="English">Faheem</option>
            <option value="Urdu">Khalil Anjum</option>
        </select>
    </div>
    <div class="form-group">
        <label for="inputAddress2">Course Name </label>
        <select id="inputState" class="form-control" required name="course">
            <option >Choose One</option>
            <option value="LMV">LMV</option>
            <option value="HTV">HTV</option>
        </select>
    </div>


    <button type="submit" class="btn btn-primary">Pushed</button>
</form>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
