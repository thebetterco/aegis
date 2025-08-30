<!DOCTYPE html>
<html>
<head>
    <title>Aegis</title>
    <style>
        body { margin: 0; display: flex; font-family: sans-serif; }
        .sidebar { width: 200px; background: #f0f0f0; padding: 1rem; height: 100vh; box-sizing: border-box; }
        .sidebar a { display: block; margin-bottom: 1rem; color: #333; text-decoration: none; }
        .content { flex: 1; padding: 1rem; }
    </style>
    @yield('head')
</head>
<body>
    <div class="sidebar">
        @auth
            <a href="{{ url('/') }}">{{ __('menu.home') }}</a>
            <a href="{{ url('/chzzk/streams') }}">{{ __('menu.chzzk_streams') }}</a>
            <a href="{{ url('/live/streams') }}">{{ __('menu.live_stream_manager') }}</a>
            @if(auth()->user()->is_superadmin)
                <a href="{{ url('/naver-commerce') }}">{{ __('menu.naver_commerce_dashboard') }}</a>
            @endif
        @else
            <a href="{{ url('/login') }}">{{ __('menu.login') }}</a>
            <a href="{{ url('/register') }}">{{ __('menu.register') }}</a>
        @endauth
    </div>
    <div class="content">
        @yield('content')
    </div>
</body>
</html>
