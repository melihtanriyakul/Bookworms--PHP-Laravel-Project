<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
    <div >
        <table >
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">ISBN</th>
                <th scope="col">Book Name</th>
                <th scope="col">Author Name</th>
                <th scope="col">Book Genre(s)</th>
                <th scope="col">Book Length</th>
                <th scope="col">Book Language</th>
                <th scope="col">Book Rate</th>
            </tr>
            </thead>
            <tbody>
            @php ($i=1)
            @foreach ($books as $book)
            <tr class = "satir" >
                <th scope="row">{{$i}}</th>
                <td>{{ $book->ISBN }}</td>
                <td>{{ $book->bookName }}</td>
                <td>{{ $book->AuthorName }}</td>
                <td>{{ $book->genreName }}</td>
                <td>{{ $book->numOfPages }}</td>
                <td>{{ $book->bookLanguage }}</td>
                <td>{{ $book->bookRate }}</td>
            </tr>
            @php ($i= $i + 1)
            @endforeach
            </tbody>
        </table>
        <br />
    </div>
    </body>
</html>
