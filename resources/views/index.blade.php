<!DOCTYPE html>
<html>
<body>
@auth
    <p>Welcome, {{ auth()->user()->email }}</p>
    @if(auth()->user()->is_superadmin)
        <a href="{{ url('/naver-commerce') }}">Naver Commerce Dashboard</a>
    @endif
    <a href="{{ url('/chzzk/streams') }}">Chzzk Streams</a>
@else
    <a href="{{ url('/login') }}">Login</a>
    <a href="{{ url('/register') }}">Register</a>
@endauth
</body>
</html>
