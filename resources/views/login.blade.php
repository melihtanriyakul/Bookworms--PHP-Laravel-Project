<!DOCTYPE html>
<html>
<head>
    <title>Login System</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" media="screen"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style type="text/css">
        .box {
            width: 600px;
            margin: 0 auto;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
<br/>
<div class="cotainer box">
    <h3 align="center">Welcome to Goodreads</h3><br/>

    @if(isset(Auth::user()->email))
        <script>window.location = "/main/userActions";</script>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert">x</button>
            <strong>{{ $message }}</strong>
        </div>
    @endif

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="post" action="{{ url('/main/checklogin')}}">
        {{ csrf_field() }}
        <div class="form-group">
            <label>Enter Email</label>
            <input type="email" name="email" class="form-control"/>
        </div>
        <div class="form-group">
            <label>Enter Password</label>
            <input type="password" name="password" class="form-control"/>
        </div>
        <div class="form-group">
            <input type="submit" name="login" class="btn btn-primary" value="Login"/>
        </div>
        <div class="form-group">
            <input type="button" onclick="window.location = '/main/signUp'" name="signUp" class="btn btn-primary"
                   value="Sign Up"/>
        </div>
    </form>
</div>
</body>
</html>