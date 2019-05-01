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
    <link href="{{ asset('css/message.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" type="text/css"
          rel="stylesheet">
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
        <a href="{{action('MainController@addUser')}}"><span class="fa fa-user-plus"
                                                             style="color:white; margin : 3px;"></span>Add Friend</a>
        <a class="active" href="{{action('MainController@message')}}"><span class="glyphicon glyphicon-envelope"
                                                                            style="color:white;margin : 3px;"></span>Messages</a>
        <a href="{{ url('/main/logout')}}"><i class="fa fa-sign-out" style="color:white;margin : 3px;"></i>Logout</a>
    </div>
</div>
<div class="container">
    <h3 class=" text-center">Messaging</h3>
    <div class="messaging">
        <div class="inbox_msg">
            <div class="inbox_people">
                <div class="headind_srch">
                    <div class="recent_heading">
                        <h4>Friends</h4>
                    </div>
                </div>
                <div class="inbox_chat">
                    @foreach($friends as $friend)
                        @if($friend->id == $selected)
                            <div class="chat_list active_chat">
                                <div class="chat_people">
                                    <div class="chat_img"><img src="https://ptetutorials.com/images/user-profile.png"
                                                               alt="sunil"></div>
                                    <div class="chat_ib">
                                        <h5>{{$friend->name}} <span class="chat_date"></span></h5>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($friend->id != $selected)
                            <div class="selected" onclick="window.location='{{ url("main/message/{$friend -> id}") }}'">
                                <div class="chat_list">
                                    <div class="chat_people">
                                        <div class="chat_img"><img
                                                    src="https://ptetutorials.com/images/user-profile.png" alt="sunil">
                                        </div>
                                        <div class="chat_ib">
                                            <h5>{{$friend->name}} <span class="chat_date"></span></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="mesgs">
                <div class="msg_history">
                    @if(isset($messages))
                        @foreach($messages as $message)
                            @if($message->recieverID == Auth::id())
                                <div class="incoming_msg">
                                    <div class="incoming_msg_img"><img
                                                src="https://ptetutorials.com/images/user-profile.png" alt="sunil">
                                    </div>
                                    <div class="received_msg">
                                        <div class="received_withd_msg">
                                            <p>{{$message->body}}</p>
                                            <span class="time_date">{{date('F-d-Y', strtotime($message->sentDate))}}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($message->senderID == Auth::id())
                                <div class="outgoing_msg">
                                    <div class="sent_msg">
                                        <p>{{$message->body}}</p>
                                        <span class="time_date">{{date('F-d-Y', strtotime($message->sentDate))}}</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
                <div class="type_msg">
                    <div class="input_msg_write">
                        <form method="POST" action="{{ url('/main/message/sendMessage')}}">
                            {{ csrf_field() }}
                            <input type="text" class="write_msg" name="body" placeholder="Type a message"/>
                            <input type="hidden" class="write_msg" name="recieverId" value="{{$selected}}"
                                   placeholder="Type a message"/>
                            <button class="msg_send_btn" type="submit"><i class="fa fa-paper-plane-o"
                                                                          aria-hidden="true"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
@toastr_css
@toastr_js
@toastr_render
</html>


