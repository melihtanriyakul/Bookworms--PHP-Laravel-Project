<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Database\Query\Builder;
use Validator;
use Auth;
use Carbon\Carbon;
use App\AuthorViewInProfile;
use App\PrintingHouseInfo;
use App\BookInfo;
use App\MessageInfo;
use App\QuoteInfo;
use App\StoreInfo;
use App\User;
use App\Genre;
use App\Author;
use App\UserAction;
use PDF;
use Illuminate\Support\Facades\Hash;

class MainController extends Controller
{
    private $user; 
    function index() {
        return view('login');
    }

    function checklogin(Request $request) {
        $this->validate($request, [
            'email'         =>  'required|email',
            'password'      =>  'required|alphaNum|min:3'
        ]);

        $user_data = array(
            'email'     =>  $request->get('email'),
            'password'  =>  $request->get('password')
        );

        if(Auth::attempt($user_data)) {
            $user = Auth::user();
            $actionBody =  $user->name.' logged in Goodreads!';
            $this->addUserAction($actionBody);
            return redirect('main/userActions');
        }
        else {
            return back()->with('error', 'You entered the wrong incredentials.');
        }
    }
    public function bookDownloadPDF(){
        $books = $books = BookInfo::all();
        $pdf = PDF::loadView('bookPDF', compact('books'));
        return $pdf->download(Carbon::now('Europe/Istanbul').'_books.pdf');
  
      }
      public function authorDownloadPDF(){
        $authors = Author::all();
        $pdf = PDF::loadView('authorPDF', compact('authors'));
        return $pdf->download(Carbon::now('Europe/Istanbul').'_authors.pdf');
  
      }
    function homePage() {
        $discussions = DB::select('select * from discussion d inner join users u on d.id = u.id inner join book b on d.ISBN = b.ISBN order by d.discussionID');
        $books = DB::select('select * from book');
        return view('homePage',['discussions' => $discussions, 'books' => $books]);
    }
    function successlogin() {
        return view('successlogin');
    }
    function users(){
        $users = DB::select('select * from users');
        return view('userInformation',['users'=>$users]);
    }
    function authors(){
        $authors = Author::all();
        return view('author',['authors'=>$authors]);
    }
    function addAuthor(Request $request){
        try{
            $authorName = $request->get('authorName');
            $dateOfBirth = $request->get('dateOfBirth');
            $dateOfDeath = $request->get('dateOfDeath');
            $biography = $request->get('biography');
            DB::table('author')->insert(
                ['authorName'=>$authorName,'dateOfBirth'=>$dateOfBirth,'dateOfDeath'=>$dateOfDeath,'biography'=>$biography]
            );
            $user = Auth::user();
            $actionBody =  $user->name.' added new Author  ('.$authorName.')';
            $this->addUserAction($actionBody);
            toastr()->success('New Author added successfully!');
            return redirect(url("main/authors"));
        }catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('New Author could not send!');
            return redirect(url("main/authors"));
        }   

    }
    function authorDetail($authorId){
        error_log($authorId);
        $authors = DB::select('select * from author where authorID = ?',[$authorId]);
        return view('authorDetail',['authors'=>$authors]);
    }
    function addUser(){
        $id = Auth::user()->id;
        $users = DB::select('select * from users u where u.id not in (select senderID from request where recieverID = ?) and u.id not in (select recieverID from request where senderID = ?)',[$id,$id]);
        $friends = DB::select('select u.id, u.name from users u  inner join (select * from friend  f where f.id = ? or f.friendId = ?) j on (u.id = j.id and j.id != ?) or (u.id = j.friendId and j.friendId != ?)',[$id,$id,$id,$id]);
        $requests = DB::select('select * from users u inner join request r on r.senderID = u.id where r.recieverID = ?',[$id]);
        return view('addUser',['users'=>$users, 'friends'=>$friends, 'requests'=>$requests]);
    }
    function sendFriendRequest(Request $request){
        try{
            $id = Auth::user()->id;
            $recieverID = $request->get('recieverID');
            DB::table('request')->insert(
                ['senderID'=>$id, 'recieverID'=>$recieverID,'sentDate'=>Carbon::now('Europe/Istanbul'),'stat'=>'pending']
            );
            toastr()->success('Request sent successfully!');
            return redirect(url("main/addUser"));
        } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('Request could not send!');
            return redirect(url("main/addUser"));
        }   
    }
    function acceptFriendRequest(Request $request){
        try{
            $id = Auth::user()->id;
            $requestID = $request->get('requestID');
            $senderID = $request->get('senderID');
            $senderUser = DB::select('select * from users where id = ?',[$senderID]);
            DB::table('request')
            ->where('requestID', $requestID)
            ->update(['stat' => 'accepted']);
            DB::table('friend')->insert(
                ['id'=>$id,'friendID'=>$senderID,'dateStarted'=>Carbon::now('Europe/Istanbul')]
            );
            toastr()->success('New Friend added successfully!');
            $user = Auth::user();
            $actionBody =  $user->name.' and '.$senderUser[0]->name.' became friends!';
            $this->addUserAction($actionBody);
            return redirect(url("main/addUser"));
        } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('New Friend could not add!');
            return redirect(url("main/addUser"));
        } 
    }
    function refuseFriendRequest(Request $request){
        try{
            $requestID = $request->get('requestID');
            DB::table('request')
            ->where('requestID', $requestID)
            ->update(['stat' => 'refused']);
            toastr()->success('Request refused successfully!');
            return redirect(url("main/addUser"));
        } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('Request could not refuse!');
            return redirect(url("main/addUser"));
        } 
    }
    function addUserAction($actionBody){
            $user = Auth::user();
            $createdDate = Carbon::now('Europe/Istanbul');
            $userID = $user->id;
            DB::table('user_actions')->insert(
                ['actionBody'=>$actionBody,'createdDate'=>$createdDate,'userID'=>$userID]
            );
    }
    function deleteDiscussion(Request $request){
        try { 
            $id = $request->id;
            $postID_List = DB::select('select postID from post where discussionID = ?',[$id]);
            $postID_List = array_map(function ($value) {
                return (array)$value;
            }, $postID_List);
            DB::table('postcomment')->whereIn('postID', $postID_List)->delete();
            DB::table('post')->where('discussionID', '=', $id)->delete();
            DB::table('discussion')->where('discussionID', '=', $id )->delete();
            toastr()->success('Discussion deleted successfully!');
            $user = Auth::user();
            $actionBody =  $user->name.' deleted a Discussion!';
            $this->addUserAction($actionBody);
            return redirect('main/homePage');
        } catch(\Illuminate\Database\QueryException $ex){ 
            error_log($ex);
            toastr()->error('Discussion could not delete!');
            return redirect('main/homePage');
        }    
    }
    function deleteComment(Request $request){
        $postID = $request->postID;
        try { 
            $id = $request->id;
            DB::table('postcomment')->where('commentID', '=', $id )->delete();
            toastr()->success('Comment deleted successfully!');
            $user = Auth::user();
            $actionBody =  $user->name.' deleted a comment from a post!';
            $this->addUserAction($actionBody);
            return redirect(url("main/comments/{$postID}"));
        } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('Comment could not delete!');
            return redirect(url("main/comments/{$postID}"));
        }    
    }
    function deletePost(Request $request){
        $discussionId = $request->discussionID;
        try { 
            $id = $request->id;
            DB::table('postcomment')->where('postID', '=', $id)->delete();
            DB::table('post')->where('postID', '=', $id )->delete();
            toastr()->success('Post deleted successfully!');
            $user = Auth::user();
            $actionBody =  $user->name.' deleted a post from a discussion!';
            $this->addUserAction($actionBody);
            return redirect(url("main/discussionDetail/{$discussionId}"));
        } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('Post could not delete!');
            return redirect(url("main/discussionDetail/{$discussionId}"));
        }

    }
    function addDiscussion(Request $request){
        try { 
            $ISBN = $request->get('bookName');
            $title = $request->get('title');
            $userId = Auth::id();
            error_log($userId);
             DB::table('discussion')->insert(
                ['ISBN' => $ISBN, 'discussionTitle' => $title, 'discussionDate' =>Carbon::now('Europe/Istanbul'), 'id' => $userId]
            );
            toastr()->success('New discussion added successfully!');
            $user = Auth::user();
            $actionBody =  $user->name.' added a new discussion  (Title : '.$title.')';
            $this->addUserAction($actionBody);
            return redirect('main/homePage');
          } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('New discussion could not add!');
            return redirect('main/homePage');
          }
       
       

    }
    function discussionDetail($id){
        $discussion = DB::select('select u.name,d.discussionTitle,d.discussionID, b.bookName from discussion d inner join users u on d.id = u.id inner join book b on d.ISBN = b.ISBN where d.discussionID = ? limit 1',[$id]);
        $posts = DB::select('select p.id,p.postID, p.postBody, p.postDate, u.name from post p  inner join users u on u.id = p.id where p.discussionID = ?',[$id]);
        return view('discussionDetail', ['posts' => $posts, 'discussion' => $discussion]);
    }
    
    function getComments($id){
        $post = DB::select('select u.name,p.postBody,p.postID,p.discussionID from post p inner join users u on p.id = u.id where p.postID = ? limit 1',[$id]);
        $comments = DB::select('select pc.id,pc.commentID, pc.commentBody, pc.postDate, u.name from postcomment pc  inner join users u on u.id = pc.id where pc.postID = ?',[$id]);
        return view('postComment', ['comments' => $comments, 'post' => $post]);
    }
    function addPost(Request $request){
        $discussionId = $request->get('discussionID');
        try {
        $userId = Auth::id();
        $post = $request->get('newPost');
        DB::table('post')->insert(
            ['discussionID' => $discussionId, 'postBody' => $post, 'postDate' =>Carbon::now('Europe/Istanbul'), 'id' => $userId]
        );
        toastr()->success('New post added successfully!');
        $user = Auth::user();
        $actionBody =  $user->name.' added a new post (Post : '.$post.')';
        $this->addUserAction($actionBody);
        return redirect(url("main/discussionDetail/{$discussionId}"));
        } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('New post could not add!');
            return redirect(url("main/discussionDetail/{$discussionId}"));
        }
    }
    function addComment(Request $request){
        $postID = $request->get('postID');
        try {
        $userId = Auth::id();
        $comment = $request->get('newComment');
        DB::table('postcomment')->insert(
            ['postID' => $postID, 'commentBody' => $comment, 'postDate' =>Carbon::now('Europe/Istanbul'), 'id' => $userId]
        );
            toastr()->success('New comment added successfully!');
            $user = Auth::user();
            $actionBody =  $user->name.' added a new comment (Comment : '.$comment.')';
            $this->addUserAction($actionBody);
        return redirect(url("main/comments/{$postID}"));
        } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('New comment could not add!');
            return redirect(url("main/comments/{$postID}"));
        }
    }
    function message(){
        $id = Auth::id();
        $friends = DB::select('select u.id, u.name from users u  inner join (select * from friend  f where f.id = ? or f.friendId = ?) j on (u.id = j.id and j.id != ?) or (u.id = j.friendId and j.friendId != ?)',[$id,$id,$id,$id]);
        return view('message',['friends' => $friends,'selected' => 0]);
    }
    function getMessages($id){
        $userId = Auth::id();
        $messages = DB::select('select * from message where (senderID = ? and recieverID = ?) or (senderID = ? and recieverID = ?) order by sentDate
        ',[$id,$userId,$userId,$id]);
        $friends = DB::select('select u.id, u.name from users u  inner join (select * from friend  f where f.id = ? or f.friendId = ?) j on (u.id = j.id and j.id != ?) or (u.id = j.friendId and j.friendId != ?)',[$userId,$userId,$userId,$userId]);
        return view('message', ['friends' => $friends,'selected' => $id,'messages' => $messages]);

    }
    function sendMessage(Request $request){
        $userId = Auth::id();
        $recieverId = $request->get('recieverId');
        $body = $request->get('body');
        DB::table('message')->insert(
            ['senderID' => $userId, 'recieverID' => $recieverId, 'sentDate' =>Carbon::now('Europe/Istanbul'), 'body' => $body]
        );
        return redirect(url("main/message/{$recieverId}"));
    }

    function profile() {
        $user = Auth::user();
        $books = DB::table('readby')
            ->join('users', 'users.id', '=', 'readby.id')
            ->join('book', 'book.isbn', '=', 'readby.isbn')
            ->where('users.id', '=',  $user->id)
            ->get();
        $authors = AuthorViewInProfile::all()->where('UserID',  $user->id);
        $id = Auth::id();
        $avgBookLength = $books->avg('numOfPages');
        $biggestBook = $books->max('numOfPages');
        $smallestBook = $books->min('numOfPages');
        $lastBook = $books->max('dateStarted');
        $firstBook = $books->min('dateStarted');
        $friends = DB::select('select * from users u  inner join (select * from friend  f where f.id = ? or f.friendId = ?) j on (u.id = j.id and j.id != ?) or (u.id = j.friendId and j.friendId != ?)',[$id,$id,$id,$id]);
        return view('profile', ['books' => $books, 'authors' => $authors, 'friends'=>$friends, 'user'=>$user, 'avgBookLength'=>$avgBookLength, 'biggestBook'=>$biggestBook, 'smallestBook'=>$smallestBook, 'lastBook'=>$lastBook, 'firstBook'=>$firstBook ]);

    }
    function deleteFriend(Request $request) {
        $id = $request->id;
        $friendid = $request->friendid;
        DB::table('friend')
            ->where('friendid', '=', $id)
            ->where('id', '=', $friendid)->delete();
        DB::table('friend')
            ->where('friendid', '=', $friendid)
            ->where('id', '=', $id)->delete();
        return redirect('/main/profile');
    }

    function updateBookDate(Request $request) {
        try {
            $ISBN = $request->input('ISBN');
            $editStart = $request->get('dateStarted');
            $editFinish = $request->get('dateFinished');
            error_log($editStart);
            $user = Auth::user();
            DB::table('readby')
                ->where('ISBN', $ISBN)
                ->where('id', $user->id)
                ->update(['dateStarted' => $editStart, 'dateFinished' => $editFinish]);
            return redirect('/main/profile');
        } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('Book couldn"t be updated!');
            return redirect('/main/books');
        }
    }
    function deleteBookFromProfile(Request $request) {
        $user = Auth::user();
        $ISBN = $request->ISBN;
        DB::table('readby')
            ->where('id', '=', $user->id)
            ->where('ISBN', '=', $ISBN)->delete();
        $actionBody =  $user->name.' removed a  book from his/her profile (Book : '.$ISBN.')';
        $this->addUserAction($actionBody);
        return redirect('/main/profile');
    }
    function addBookMyProfile(Request $request){
        $ISBN = $request->ISBN;
        $user = Auth::user();
        DB::table('readBy')->insert(
            ['ISBN' => $ISBN, 'id' => $user->id, 'dateStarted' =>Carbon::now('Europe/Istanbul'), 'readingStatus' => 'Reading']
        );
        $actionBody =  $user->name.' added a  book from his/her profile (Book : '.$ISBN.')';
        $this->addUserAction($actionBody);
        return redirect('/main/books');
    }
    function publisher() {
        $publishers = DB::table('publisher')->get();
        return view('publisher', ['publishers'=>$publishers]);
    }
    function printinghouse() {
        $printinghouses = PrintingHouseInfo::all();
        $publishers = DB::table('publisher')->get();
        return view('printinghouse', ['printinghouses'=>$printinghouses, 'publishers'=>$publishers]);
    }

    function books() {
        $user = Auth::user();
        $books = BookInfo::all();
        $genres = Genre::all();
        $authors = Author::all();
        $myBooks = DB::table('readby')
        ->join('users', 'users.id', '=', 'readby.id')
        ->join('book', 'book.isbn', '=', 'readby.isbn')
        ->where('users.id', '=',  $user->id)
        ->get();
        return view('books', ['books'=>$books, 'myBooks'=> $myBooks,'genres' => $genres, 'authors'=> $authors]);
    }
    function messages() {
        $user = Auth::user();
        $messages = DB::select('select * from message_info where senderid= ? or receiverid = ?',[$user->id,$user->id]);
        return view('messages', ['messages'=>$messages]);
    }
    function quotes() {
        $quotes = QuoteInfo::all();
        $authors = DB::table('author')->get();
        return view('quotes', ['quotes'=>$quotes, 'authors'=>$authors]);
    }
    function stores() {
        $stores = StoreInfo::all();
        $publishers = DB::table('publisher')->get();
        return view('stores', ['stores'=>$stores, 'publishers'=>$publishers]);
    }
    function signUp(){
        return view('signUp');
    }
    function register(Request $request){
        $name = $request->get('name');
        $email = $request->get('email');
        $password = $request->get('password');
        $user = new User();
        $user->name = $name;
        $user->password = Hash::make($password);
        $user->email = $email;
        $user->typeID = 1;
        $user->save();
        return view('login');
    }
    function newBook(Request $request){
        try{
            $ISBN = $request->get('ISBN');
            $author = $request->get('author');
            $dateWritten = $request->get('dateWritten');
            $genres = $request->get('genre');
            $bookName = $request->get('bookName');
            $numOfPages = $request->get('numOfPages');
            $bookLanguage = $request->get('bookLanguage');
            DB::table('book')->insert(
                ['ISBN' => $ISBN, 'bookName' => $bookName, 'numOfPages' =>$numOfPages, 'bookLanguage' => $bookLanguage]
            );
            foreach ($genres as $genreID){ 
                DB::table('genreofbook')->insert(
                    ['ISBN' => $ISBN, 'genreID' => $genreID]
                );
            }
            DB::table('writtenby')->insert(
                ['ISBN' => $ISBN, 'authorID' => $author, 'dateWritten' =>$dateWritten]
            );
            toastr()->success('New book added successfully!');
            $user = Auth::user();
            $actionBody =  $user->name.' added new Book! ('.$bookName.')';
            $this->addUserAction($actionBody);
            return redirect('/main/books');
        } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('New book could not add!');
            return redirect('/main/books');
        }

    }
    function bookReview($ISBN){
        $book = DB::select('select * from book where ISBN = ? limit 1',[$ISBN]);
        $reviews = DB::select('select * from bookreview br inner join users u on u.id = br.id where br.ISBN = ?',[$ISBN]);
        return view('bookReview',['book' => $book, 'reviews' => $reviews]);
    }
    function addReview(Request $request){
        $ISBN = $request->get('ISBN');
        try{
            $userId = Auth::id();
            $bookRate = $request->get('bookRate');
            $newReview = $request->get('newReview');
            $bookName = DB::select('select bookName from book where ISBN =?',[$ISBN]);
            DB::table('bookreview')->insert(
                ['ISBN'=>$ISBN, 'bookReviewRate'=>$bookRate,'id'=>$userId,'bookReviewBody'=>$newReview,'dateOfBookReview'=>Carbon::now('Europe/Istanbul')]
            );
            toastr()->success('New review added successfully!');
            $user = Auth::user();
            $actionBody =  $user->name.' added new review a book! ('.$bookName[0]->bookName.')';
            $this->addUserAction($actionBody);
            return redirect(url("/main/books/review/{$ISBN}"));
        } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('New review could not add!');
            return redirect(url("/main/books/review/{$ISBN}"));
        }
    }
    function deleteReview(Request $request){
        $ISBN = $request->get('ISBN');
        try{
            $id = $request->id;
            $bookName = DB::select('select bookName from book where ISBN =?',[$ISBN]);
            DB::table('bookreview')
                ->where('bookReviewID', '=', $id)->delete();
            toastr()->success('Review deleted successfully!');
            $user = Auth::user();
            $actionBody =  $user->name.' removed  review from a book! ('.$bookName[0]->bookName.')';
            $this->addUserAction($actionBody);
            return redirect(url("/main/books/review/{$ISBN}"));
        }catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('Review could not delete!');
            return redirect(url("/main/books/review/{$ISBN}"));
        }
    }
    function userActions(){
        $userActions = DB::select('select * from user_actions order by createdDate desc limit 10');
        return view('userActions',['userActions'=>$userActions]);
    }
    function addPrintingHouse(Request $request) {
        try { 
            $publisherID = $request->get('publisherName');
            $phousename = $request->get('pHouseName');
            $address = $request->get('address');
            $userId = Auth::id();
             DB::table('printinghouse')->insert(
                ['publisherID' => $publisherID, 'printingHouseAddress' => $address]
            );
            toastr()->success('Transaction completed successfully!');
            return redirect('main/printinghouse');
          } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('The transaction failed!');
            return redirect('main/printinghouse');
          }
    }

    function addStore(Request $request) {
        try { 
            $publisherID = $request->get('publisherName');
            $storeName = $request->get('storeName');
            $address = $request->get('address');
            $userId = Auth::id();
             DB::table('store')->insert(
                ['publisherID' => $publisherID, 'storeName' => $storeName,'storeAddress' => $address]
            );
            toastr()->success('Transaction completed successfully!');
            return redirect('main/stores');
          } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('The transaction failed!');
            return redirect('main/stores');
          }
    }

    function deleteStore(Request $request) {
        try {
            $storeName = $request->storeName;
            DB::table('store')
                ->where('storeID', '=', $storeName)
                ->delete();
            toastr()->success('Transaction completed successfully!');
            return redirect('/main/stores');
        } catch (\Illuminate\Database\QueryException $ex) {
                toastr()->error('The transaction failed!');
                return redirect('main/stores');        
        }
    }

    function updateStore(Request $request) {
        try {
            $storeID = $request->input('storeID');
            $editStore = $request->get('editStore');
            $editAddress = $request->get('editAddress');
            DB::table('store')
                ->where('storeID', $storeID)
                ->update(['storeName' => $editStore, 'storeAddress' => $editAddress]);
            return redirect('/main/stores');
        } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('Book couldn"t be updated!');
            return redirect('/main/stores');
        }
    }

    function deletePrintingHouse(Request $request) {
        try {
            $PHouseID = $request->get('PHouseID');
            error_log($PHouseID);
            DB::table('printinghouse')
                ->where('printingHouseID', '=', $PHouseID)
                ->delete();
            toastr()->success('Transaction completed successfully!');
            return redirect('/main/printinghouse');
        } catch (\Illuminate\Database\QueryException $ex) {
                toastr()->error('The transaction failed!');
                return redirect('main/printinghouse');        
        }
    }

    function updatePrintingHouse(Request $request) {
        try {
            $printingHouseID = $request->get('printingHouseID');
            $editAddress = $request->get('editAddress');
            DB::table('printinghouse')
                ->where('printingHouseID', $printingHouseID)
                ->update(['printingHouseAddress' => $editAddress]);
            toastr()->success('Transaction completed successfully!');
            return redirect('/main/printinghouse');
        } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('Book couldn"t be updated!');
            return redirect('/main/printinghouse');
        }
    }

    
    function addPublisher(Request $request) {
        try { 
            $publisherName = $request->get('publisherName');
            $founder = $request->get('founder');
            $origin = $request->get('origin');
            $dateFounded = $request->get('dateFounded');
             DB::table('publisher')->insert(
                ['publisherName' => $publisherName, 'founder' => $founder, 'origin'=>$origin, 'dateFounded'=>$dateFounded]
            );
            toastr()->success('Transaction completed successfully!');
            return redirect('main/publisher');
          } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('The transaction failed!');
            return redirect('main/publisher');
          }
    }
    function updatePublisher(Request $request) {
        try {
            $editName = $request->get('editName');
            $editFounder = $request->get('editFounder');
            $editOrigin = $request->get('editOrigin');
            $editDateFounded = $request->get('editDateFounded');
            $publisherID = $request->get('publisherID');
            DB::table('publisher') 
                ->where('publisherID', $publisherID)
                ->update(['publisherName' => $editName, 'founder'=> $editFounder, 'origin'=> $editOrigin, 'dateFounded'=>$editDateFounded]);
            toastr()->success('Transaction completed successfully!');
            return redirect('/main/publisher');
        } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('Book couldn"t be updated!');
            return redirect('/main/publisher');
        }
    }

    function addQuote(Request $request) {
        try {
            $quote = $request->get('quote');
            $author = $request->get('author');
             DB::table('quoteofauthor')->insert(
                ['authorID' => $author, 'quoteBody' => $quote]);
            toastr()->success('Transaction completed successfully!');
            return redirect('main/quotes');
        } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('The transaction failed!');
            return redirect('main/quotes');
        }
    }

    function deleteQuote(Request $request) {
        try {
            $quoteID = $request->get('quoteID');
            DB::table('quoteofauthor')
                ->where('quoteID', '=', $quoteID)
                ->delete();
            toastr()->success('Transaction completed successfully!');
            return redirect('/main/quotes');
        } catch (\Illuminate\Database\QueryException $ex) {
                toastr()->error('The transaction failed!');
                return redirect('main/quotes');        
        }
    }

    function updateQuote(Request $request) {
        try {
            $quoteID = $request->input('quoteID');
            $editQuote = $request->get('editQuote');
            DB::table('quoteofauthor')
                ->where('quoteID', $quoteID)
                ->update(['quoteBody' => $editQuote]);
            toastr()->success('Transaction completed successfully!');
            return redirect('/main/quotes');
        } catch(\Illuminate\Database\QueryException $ex){ 
            toastr()->error('Book couldn"t be updated!');
            return redirect('/main/quotes');
        }
    }
    function logout() {
        $user = Auth::user();
        $actionBody =  $user->name.' logged out from Goodreads!';
        $this->addUserAction($actionBody);
        Auth::logout();
        return redirect('main');
    }
}
