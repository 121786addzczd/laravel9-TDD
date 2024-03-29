<?php

namespace App\Http\Controllers;

use App\Actions\StrRandom;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::query()
            ->onlyOpen()
            ->with('user')
            ->withCount('comments')
            ->orderByDesc('comments_count')
            ->get();

        return view('index', compact('posts'));
    }

    public function show(Post $post, StrRandom $strRandom)
    {
        if ($post->isClosed()) {
            abort(403);
        }

        $random = $strRandom->get(10);

        return view('posts.show', compact('post', 'random'));
    }
}
