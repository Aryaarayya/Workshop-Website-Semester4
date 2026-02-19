<!DOCTYPE html>
<html>
    <head>
        <title>Daftar Post</title>
</head>
<body>  
    <h1>Daftar Post</h1>
    @foreach($post as $post)
    <h2>{{ $post->title }}</h2>
    <p>{{ $post->content }}</p>
    @endforeach
</body>
</html>