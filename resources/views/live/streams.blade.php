@extends('layouts.app')

@section('content')
<h1>Live Streams</h1>
<ul>
@foreach ($streams as $stream)
    <li>
        <a href="{{ url('/live/streams/'.$stream->id) }}">{{ $stream->title }}</a>
        - {{ $stream->status }}
    </li>
@endforeach
</ul>
@endsection
