<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div>
    <table>
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Author</th>
            <th scope="col">Date of Birth</th>
            <th scope="col">Date of Death</th>
        </tr>
        </thead>
        <tbody>
        @php ($i=1)
        @foreach ($authors as $author)
            <tr class="satir">
                <th scope="row">{{$i}}</th>
                <td>{{$author ->authorName}}</td>
                <td>{{$author ->dateOfBirth}}</td>
                <td>{{$author ->dateOfDeath}}</td>
            </tr>
            @php ($i = $i+1)
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>


