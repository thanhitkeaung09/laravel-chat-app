@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Chat</div>
                <div class="card-body">
                    <ul id="messages" class="list-unstyled mb-4" style="height: 300px; overflow-y: auto;">
                        <!-- Messages will be appended here -->
                    </ul>
                    <form id="message-form">
                        <div class="input-group">
                            <input id="message-input" type="text" class="form-control" placeholder="Type a message..." required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Send</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

<script>
    Pusher.logToConsole = true;

    var pusher = new Pusher('c44cf113afdc1503a458', {
        cluster: 'ap1'
    });

    var channel = pusher.subscribe('chat');
    channel.bind('message', function(data) {
        var messages = document.getElementById('messages');
        var isNearBottom = messages.scrollTop + messages.clientHeight >= messages.scrollHeight - 50;

        var messageElement = document.createElement('li');
        messageElement.textContent = data.message.body;
        console.log(data.message.user_id);
        messageElement.className = data.message.user_id == "{{ Auth::id() }}" ? 'message-owner' : 'message-visitor';
        messages.appendChild(messageElement);

        if (isNearBottom) {
            messages.scrollTop = messages.scrollHeight;
        }
    });

    document.getElementById('message-form').addEventListener('submit', function(e) {
        e.preventDefault();
        var message = document.getElementById('message-input').value;

        fetch('/send-message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: message, user_id: {{ Auth::id() }} })
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  document.getElementById('message-input').value = '';
                  var messages = document.getElementById('messages');
                  messages.scrollTop = messages.scrollHeight;
              }
          });
    });

    // Scroll to the bottom on page load
    window.onload = function() {
        var messages = document.getElementById('messages');
        messages.scrollTop = messages.scrollHeight;
    };
</script>

<style>
    .message-owner {
        text-align: left;
        background-color: #e1ffc7;
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 10px;
        max-width: 60%;
        margin-left: 0;
    }

    .message-visitor {
        text-align: right;
        background-color: #f1f0f0;
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 10px;
        max-width: 60%;
        margin-left: auto;
    }

    #messages {
        overflow-y: auto;
        height: 300px;
        padding-right: 10px;
    }
</style>
@endsection
