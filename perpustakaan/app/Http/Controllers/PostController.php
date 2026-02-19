<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $post = Post::all(); // Mengambil semua data dari tabel posts
        return view('post.index', compact('post')); // Mengirim data ke View
    }
}
