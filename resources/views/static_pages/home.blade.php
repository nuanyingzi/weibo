@extends('layouts.default')

@section('content')
  <div class="bg-light p-3 p-sm-5 rounded">
    <h1>Look my eyes</h1>
    <p class="head">
      Tell me, <a href="https://learnku.com/courses/laravel-essential-training">why</a> ?
    </p>
    <p>Baby, tell me, why?</p>
    <p>
      <a class="btn btn-lg btn-success" href="{{ route('signup') }}" role="button">注册</a>
    </p>
  </div>
@stop
