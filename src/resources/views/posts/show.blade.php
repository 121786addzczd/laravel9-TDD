@extends('layouts.index')

@section('content')

@if(today()->is('12-25'))
<h1>メリークリスマス！</h1>
@endif

<h1>{{ $post->title }}</h1>
<div>{!! nl2br(e($post->body)) !!}</div>

<p>書き手：{{ $post->user->name }}</p>


@endsection
