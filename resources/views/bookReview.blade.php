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
    <a class="active" href="{{ url('/main/books') }}">Books</a>
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
        <a href="{{action('MainController@addUser')}}"><span class="fa fa-user-plus"
                                                             style="color:white; margin : 3px;"></span>Add Friend</a>
        <a href="{{action('MainController@message')}}"><span class="glyphicon glyphicon-envelope"
                                                             style="color:white;margin : 3px;"></span>Messages</a>
        <a href="{{ url('/main/logout')}}"><i class="fa fa-sign-out" style="color:white;margin : 3px;"></i>Logout</a>
    </div>
</div>
<div class="panel panel-success">
    <div class="panel-heading">Book : {{$book[0]->bookName}}</div>
</div>
<a href="{{action('MainController@books')}}">
    <span class="glyphicon glyphicon-circle-arrow-left" style="font-size : 35px !important;margin-left : 20px;">
    <p style="font-size : 20px;">Back</p>
    </span>
</a>
<div class="table-wrapper-scroll-y">

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Review Owner</th>
            <th scope="col">Review</th>
            <th scope="col">Date</th>
        </tr>
        </thead>
        <tbody>
        @php ($i=1)
        @foreach ($reviews as $review)
            <tr class="satir">
                <th scope="row">{{$i}}</th>
                <td>{{$review ->name}}</td>
                <td>{{$review ->bookReviewBody}}</td>
                <td>{{$review ->dateOfBookReview}}</td>
                @if(Auth::id()  == $review->id )
                    <td>
                        <a href="{{action('MainController@deleteReview',['id' =>$review -> bookReviewID, 'ISBN' => $review->ISBN])}}"><span
                                    class="glyphicon glyphicon-trash"></span></a></td>
                @endif
            </tr>
            @php ($i = $i+1)
        @endforeach
        </tbody>
    </table>
</div>
<div class="newRecord">
    <form method="POST" action="{{ url('/main/books/addReview')}}">
        {{ csrf_field() }}
        <div class="flex form-group">
            <label for="usr">New Review:</label>
            <input type="text" class="form-control" name="newReview">
            <input type="hidden" class="form-control" name="ISBN" value="{{$book[0]->ISBN}}">
        </div>
        <div class="flex form-group">
            <label for="usr">Book Rate: </label>
            <label class="radio-inline">
                <input type="radio" value="1" name="bookRate" checked>1
            </label>
            <label class="radio-inline">
                <input type="radio" value="2" name="bookRate">2
            </label>
            <label class="radio-inline">
                <input type="radio" value="3" name="bookRate">3
            </label>
            <label class="radio-inline">
                <input type="radio" value="4" name="bookRate">4
            </label>
            <label class="radio-inline">
                <input type="radio" value="5" name="bookRate">5
            </label>
        </div>
        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>
</body>
@toastr_css
@toastr_js
@toastr_render
</html>


