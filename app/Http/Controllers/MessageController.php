<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = new Message();
        $message->body = $request->message;
        $message->user_id = Auth::id();
        $message->save();

        // broadcast(new MessageSent('asldjflasdf'));
        // event(new MessageSent('hello world new'));
        $pusher = new Pusher(
            "c44cf113afdc1503a458",
            "4877ac6343158be57aad",
            "1825395",
            array('cluster' => 'ap1')
          );
          
          $pusher->trigger('chat', 'message', array('message' => $message));

        
        return response()->json(['success' => true, 'message' => $message]);
    }
}
