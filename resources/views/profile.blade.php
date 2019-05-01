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
    <a href="{{ url('/main/publisher') }}">Publishers</a>
    <a href="{{ url('/main/stores') }}">Stores</a>
    <a href="{{ url('/main/authors') }}">Authors</a>
    @if(Auth::user()->typeID == 0)
        <a href="{{action('MainController@users')}}">Users</a>
    @endif
    <div class="topnav-right">
        <a class="active" href="{{ url('/main/profile') }}"><i class="fa fa-fw fa-user"
                                                               style="color:white; margin : 3px;"></i>{{Auth::user()->name}}
        </a>
        <a href="{{action('MainController@addUser')}}"><span class="fa fa-user-plus"
                                                             style="color:white; margin : 3px;"></span>Add Friend</a>
        <a href="{{action('MainController@message')}}"><span class="glyphicon glyphicon-envelope"
                                                             style="color:white;margin : 3px;"></span>Messages</a>
        <a href="{{ url('/main/logout')}}"><i class="fa fa-sign-out" style="color:white;margin : 3px;"></i>Logout</a>
    </div>
</div>
<h1 id="profileNameTopHeading" class="userProfileName" style="margin-left: 10px;">
    {{ $user->name }}
</h1>
<div class="infoBoxRowItem" style="margin-left: 10px;">
    {{ $user->email }}
</div>
<div class="table-wrapper-scroll-y">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th scope="col">Friends</th>
            <th scope="col">Books Read</th>
            <th scope="col">Author Read</th>
            <th scope="col">First Book</th>
            <th scope="col">Last Book</th>
            <th scope="col">Average Book Length</th>
            <th scope="col">Biggest Book Length</th>
            <th scope="col">Smallest Book Length</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{ count($friends) }}</td>
            <td>{{ count($books) }}</td>
            <td>{{ count($authors) }}</td>
            <td>{{ $firstBook }}</td>
            <td>{{ $lastBook }}</td>
            <td>{{ $avgBookLength }}</td>
            <td>{{ $biggestBook }}</td>
            <td>{{ $smallestBook }}</td>
        </tr>
        </tbody>
    </table>
</div>
<div class="table-wrapper-scroll-y">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Book Name</th>
            <th scope="col">Book ISBN</th>
            <th scope="col">Book Language</th>
            <th scope="col">Book Length</th>
            <th scope="col">Date Started</th>
            <th scope="col">Date Finished</th>
        </tr>
        </thead>
        <tbody>
        @php ($i=1)
        @foreach ($books as $book)
            <script>
                $(document).ready(function () {
                    $("#editButton{{$book->ISBN}}").click(function () {
                        $(".readDate{{$book->ISBN}}").show();
                        $("#okButton{{$book->ISBN}}").show();
                    });
                });
            </script>
            <tr>
                <th scope="row">{{$i}}</th>
                <td>{{ $book->bookName }}</td>
                <td>{{ $book->ISBN }}</td>
                <td>{{ $book->numOfPages }}</td>
                <td>{{ $book->bookLanguage }}</td>

                <form method="POST" action="{{ url('/main/updateBookDate')}}">
                    {{ csrf_field() }}
                    <input type="text" value="{{$book -> ISBN}}" name="ISBN" hidden/>
                    <td>{{ $book->dateStarted }}<input class="readDate{{$book->ISBN}}" type="date"
                                                       value="{{ $book->dateStarted }}" name="dateStarted" hidden/></td>
                    <td>{{ $book->dateFinished }}<input class="readDate{{$book->ISBN}}" type="date"
                                                        value="{{ $book->dateFinished }}" name="dateFinished" hidden/>
                    </td>
                    <td>
                        <button id="editButton{{$book->ISBN}}" type="button"><span
                                    class="glyphicon glyphicon-edit"></span></button>
                    </td>
                    <td>
                        <button id="okButton{{$book->ISBN}}" type="submit" hidden><span
                                    class="glyphicon glyphicon-ok"></span></button>
                    </td>
                </form>
                <td><a href="{{action('MainController@deleteBookFromProfile',['ISBN' =>$book -> ISBN])}}"><span
                                class="glyphicon glyphicon-trash"></span></a></td>
            </tr>
            @php ($i= $i + 1)
        @endforeach
        </tbody>
    </table>
    <br/>
</div>
<div class="table-wrapper-scroll-y">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Author Name</th>
            <th scope="col">Date of Birth</th>
            <th scope="col">Date of Death</th>
            <th scope="col">Biography</th>
        </tr>
        </thead>
        <tbody>
        @php ($i=1)
        @foreach ($authors as $author)
            <tr>
                <th scope="row">{{$i}}</th>
                <td>{{ $author->AuthorName }}</td>
                <td>{{ $author->DateOfBirth }}</td>
                <td>{{ $author->DateOfDeath }}</td>
                <td>{{ $author->AuthorBiography }}</td>
            </tr>
            @php ($i= $i + 1)
        @endforeach
        </tbody>
    </table>
    <br/>
</div>
<div class="table-wrapper-scroll-y">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Friend Name</th>
            <th scope="col">Friend Email</th>
        </tr>
        </thead>
        <tbody>
        @php ($i=1)
        @foreach ($friends as $friend)
            <tr>
                <th scope="row">{{$i}}</th>
                <td>{{ $friend->name }}</td>
                <td>{{ $friend->email }}</td>
                <td>
                    <a href="{{action('MainController@deleteFriend',['id' =>$friend -> friendid, 'friendid'=>$friend -> id])}}"><span
                                class="glyphicon glyphicon-trash"></span></a></td>
            </tr>
            @php ($i = $i + 1)
        @endforeach
        </tbody>
    </table>
</div>
</div>
</body>
@toastr_css
@toastr_js
@toastr_render
</html>