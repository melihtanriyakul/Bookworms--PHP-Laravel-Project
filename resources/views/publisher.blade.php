<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" media="screen"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="{{ asset('css/homePage.css') }}" rel="stylesheet">
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
    <a class="active" href="{{ url('/main/publisher') }}">Publishers</a>
    <a href="{{ url('/main/stores') }}">Stores</a>
    <a href="{{ url('/main/authors') }}">Authors</a>
    @if(Auth::user()->typeID == 0)
        <a href="{{action('MainController@users')}}">Users</a>
    @endif
    <div class="topnav-right">
        <a href="{{ url('/main/profile') }}"><i class="fa fa-fw fa-user"
                                                style="color:white; margin : 3px;"></i>{{Auth::user()->name}}</a>
        <a href="{{action('MainController@addUser')}}"><span class="fa fa-user-plus"
                                                             style="color:white; margin : 3px;"></span>Add Friend</a>
        <a href="{{action('MainController@message')}}"><span class="glyphicon glyphicon-envelope"
                                                             style="color:white;margin : 3px;"></span>Messages</a>
        <a href="{{ url('/main/logout')}}"><i class="fa fa-sign-out" style="color:white;margin : 3px;"></i>Logout</a>
    </div>
</div>

<div class="table-wrapper-scroll-y">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Publisher Name</th>
            <th scope="col">Founder</th>
            <th scope="col">Country of origin</th>
            <th scope="col">Date Founded</th>
        </tr>
        </thead>
        <tbody>
        @php ($i=1)
        @foreach ($publishers as $publisher)
            <script>
                $(document).ready(function () {
                    $("#editButton{{$i}}").click(function () {
                        $(".{{$i}}").show();
                        $("#okButton{{$i}}").show();
                    });
                });
            </script>
            <tr>
                <form method="POST" action="{{ url('/main/updatePublisher')}}">
                    {{ csrf_field() }}

                    <th scope="row">{{$i}}</th>
                    <td>{{ $publisher->publisherName }}<input class="{{$i}}" value="{{ $publisher->publisherName }}"
                                                              type="text" name="editName" hidden/></td>
                    <td>{{ $publisher->founder }}<input class="{{$i}}" type="text" value="{{ $publisher->founder }}"
                                                        name="editFounder" hidden/></td>
                    <td>{{ $publisher->origin }}<input class="{{$i}}" type="text" value="{{ $publisher->origin }}"
                                                       name="editOrigin" hidden/></td>
                    <td>{{ $publisher->dateFounded }}<input class="{{$i}}" type="text"
                                                            value="{{ $publisher->dateFounded }}" name="editDateFounded"
                                                            hidden/></td>
                    <input class="publisherID" name="publisherID" value="{{ $publisher->publisherID }}" hidden/>
                    @if(Auth::user()->typeID == 0)
                        <td>
                            <button id="editButton{{$i}}" type="button"><span class="glyphicon glyphicon-edit"></span>
                            </button>
                        </td>
                        <td>
                            <button id="okButton{{$i}}" type="submit" hidden><span
                                        class="glyphicon glyphicon-ok"></span></button>
                        </td>
                    @endif
                </form>

            </tr>
            @php ($i= $i + 1)
        @endforeach
        </tbody>
    </table>
    <br/>
</div>
@if(Auth::user()->typeID == 0)
    <div class="newRecord">
        <div class="new">
            <span class="label label-default">New Publisher</span>
        </div>
        <form method="POST" action="{{ url('/main/addPublisher')}}">
            {{ csrf_field() }}
            <div class="flex form-group">
                <label for="usr">Publisher Name:</label>
                <input type="text" class="form-control" name="publisherName">
            </div>
            <div class="flex form-group">
                <label for="usr">Founder:</label>
                <input type="text" class="form-control" name="founder">
            </div>
            <div class="form-group">
                <label for="usr">Country of origin:</label>
                <input type="text" class="form-control" name="origin">
            </div>
            <div class="flex form-group">
                <label for="usr">Date Founded(Year):</label>
                <input type="text" class="form-control" name="dateFounded">
            </div>
            <button type="submit" class="btn btn-success">Save</button>
        </form>
    </div>
@endif
</body>
@toastr_css
@toastr_js
@toastr_render
</html>