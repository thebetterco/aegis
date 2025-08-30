@extends('layouts.app')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
.chat { border:1px solid #ccc; width:300px; height:400px; overflow-y:scroll; }
.chat-line { padding:2px; }
#userModal { position:fixed; top:20%; left:40%; background:#fff; border:1px solid #333; padding:10px; }
</style>
@endsection

@section('content')
<h2>{{ $stream->title }}</h2>
<video id="player" src="{{ $stream->url }}" controls width="640"></video>
<div class="chat" id="chat">
    <div class="chat-line"><span class="chat-user" data-user-id="123">user123</span>: hello world</div>
</div>
<div id="userModal" style="display:none;">
    <div id="modalContent"></div>
    <button id="muteBtn">Mute</button>
    <button id="banBtn">Ban</button>
    <button onclick="document.getElementById('userModal').style.display='none'">Close</button>
</div>
<script>
document.querySelectorAll('.chat-user').forEach(el => {
    el.addEventListener('click', () => {
        const userId = el.dataset.userId;
        fetch(`/chat/user/${userId}`)
            .then(r => r.json())
            .then(data => {
                document.getElementById('modalContent').innerHTML =
                    `<p>Nicknames: ${data.nicknames.join(', ')}</p>` +
                    `<p>Past chats:</p><ul>` +
                    data.chats.map(c => `<li>${c.message}</li>`).join('') +
                    `</ul><p>Sanctions: ${data.sanctions}</p>`;
                document.getElementById('muteBtn').onclick = () => act('mute', userId);
                document.getElementById('banBtn').onclick = () => act('ban', userId);
                document.getElementById('userModal').style.display = 'block';
            });
    });
});
function act(type, userId) {
    fetch(`/chat/${type}/${userId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(r => r.json()).then(data => alert(data.status));
}
</script>
@endsection
