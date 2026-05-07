<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat');
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'username' => 'required|string|max:50',
        ]);

        broadcast(new MessageSent(
            message: $request->message,
            username: $request->username,
            timestamp: now()->format('H:i'),
        ));

        return response()->json(['status' => 'Message sent!']);
    }
}