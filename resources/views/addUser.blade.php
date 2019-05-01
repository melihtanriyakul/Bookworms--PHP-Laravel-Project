<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" media="screen"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.css" rel="stylesheet"/> -->
    <link href="{{ asset('css/homePage.css') }}" rel="stylesheet">
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> -->
</head>
<body>
<div class="topnav">
    <a href="{{action('MainController@userActions')}}">Home</a>
    <a href="{{action('MainController@homePage')}}">Discussion</a>
    <a href="{{ url('/main/quotes') }}">Quotes</a>
    <a href="{{ url('/main/books') }}">Books</a>
    @if(Auth::user()->typeID == 0)
        <a href="{{ url('/main/printinghouse') }}">Printing Houses</a>
    @endif
    <a href="{{ url('/main/publisher') }}">Publishers</a>
    <a href="{{ url('/main/stores') }}">Stores</a>
    <a href="{{ url('/main/authors') }}">Authors</a>
    @if(Auth::user()->typeID == 0)
        <a href="{{action('MainController@users')}}">Users</a>
    @endif
    <div class="topnav-right">
        <a href="{{ url('/main/profile') }}"><i class="fa fa-fw fa-user"
                                                style="color:white; margin : 3px;"></i>{{Auth::user()->name}}</a>
        <a class="active" href="{{action('MainController@addUser')}}"><span class="fa fa-user-plus"
                                                                            style="color:white; margin : 3px;"></span>Add
            Friend</a>
        <a href="{{action('MainController@message')}}"><span class="glyphicon glyphicon-envelope"
                                                             style="color:white;margin : 3px;"></span>Messages</a>
        <a href="{{ url('/main/logout')}}"><i class="fa fa-sign-out" style="color:white;margin : 3px;"></i>Logout</a>
    </div>
</div>
<h3 class="myH3">Friendship Requests</h3>
<div class="table-wrapper-scroll-y">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Date</th>
            <th scope="col">Accept</th>
            <th scope="col">Refuse</th>
        </tr>
        </thead>
        <tbody>
        @php ($i=1)
        @foreach ($requests as $request)
            @if($request->stat == 'pending')
                <tr class="satir">
                    <th scope="row">{{$i}}</th>
                    <td>{{$request ->name}}</td>
                    <td>{{$request ->sentDate}}</td>
                    <td>
                        <a href="{{action('MainController@acceptFriendRequest',['requestID' =>$request -> requestID, 'senderID'=>$request->senderID])}}"><span
                                    class="fa fa-check" style="color: green"></span></a></td>
                    <td>
                        <a href="{{action('MainController@refuseFriendRequest',['requestID' =>$request -> requestID])}}"><span
                                    class="fa fa-times" style="color: red"></span></a></td>
                </tr>
            @endif
            @php ($i = $i+1)
        @endforeach
        </tbody>
    </table>
</div>
<h3 class="myH3">Users</h3>
<div class="table-wrapper-scroll-y">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
        </tr>
        </thead>
        <tbody>
        @php ($i=1)
        @foreach ($users as $user)
            @if($user->typeID == 1 || Auth::user()->typeID == 0)
                @if($user->id != Auth::user()->id)
                    @php($exist = 0)
                    @foreach($friends as $friend)
                        @if($friend->id == $user->id)
                            @php($exist = 1)
                        @endif
                    @endforeach
                    @if($exist == 0)
                        <tr class="satir">
                            <th scope="row">{{$i}}</th>
                            <td>{{$user ->name}}</td>
                            <td>
                                <a href="{{action('MainController@sendFriendRequest',['recieverID' =>$user -> id])}}"><span
                                            class="glyphicon glyphicon-plus" style="color: green"></span></a></td>
                        </tr>
                    @endif
                    @php ($i = $i+1)
                @endif
            @endif
        @endforeach
        </tbody>
    </table>
</div>
</body>
@toastr_css
@toastr_js
@toastr_render
</html>


