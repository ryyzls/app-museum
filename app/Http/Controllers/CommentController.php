<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::latest()->get();
        return view('about', compact('comments'));
    }

    public function store(Request $request)
    {
    Comment::create([
    'name' => Auth::user()->name,
    'message' => $request->message
    ]);

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        $comment->update([
            'name' => $request->name,
            'message' => $request->message
        ]);

        return redirect()->back();
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return redirect()->back();
    }
}