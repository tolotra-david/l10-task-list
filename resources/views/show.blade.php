@extends('layouts.app')

@section('title', $task->title)
    
@section('content')
    <p>{{$task->description}}</p>
    @if ($description = $task->long_description)
    <p>{{$description}}</p>
    @endif
    <p>{{$task->created_at}}</p>
    <p>{{$task->updated_at}}</p>
@endsection