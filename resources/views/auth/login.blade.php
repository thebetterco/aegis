@extends('layouts.app')

@section('content')
@if(session('status'))
    <p>{{ session('status') }}</p>
@endif
@if($errors->any())
    <div>{{ $errors->first() }}</div>
@endif
<form method="POST" action="{{ url('/login') }}">
    @csrf
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="checkbox" name="remember"> Remember me
    <button type="submit">Login</button>
</form>
<a href="{{ route('oauth.chzzk') }}">Login with Naver Chzzk</a>
<a href="{{ route('oauth.youtube') }}">Login with YouTube</a>
@endsection
