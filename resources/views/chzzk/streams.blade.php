@extends('layouts.app')

@section('content')
<h1>Chzzk Streams</h1>
<ul>
@foreach($streams as $stream)
    <li><a href="{{ url('/chzzk/streams/'.$stream->filename) }}">{{ $stream->filename }}</a></li>
@endforeach
</ul>
@endsection
