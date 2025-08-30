@extends('layouts.app')

@section('content')
    @auth
        <p>Welcome, {{ auth()->user()->email }}</p>
    @else
        <p>Please login.</p>
    @endauth
@endsection
