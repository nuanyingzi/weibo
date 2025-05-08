@extends('layouts.default')

@section('content')
  @if (Auth::check())
    <div class="row">
      <div class="col-md-8">
        <section class="status_form">
          @include('shared._status_form')
        </section>
        <h4>微博列表</h4>
        @include('shared._feed')
      </div>
      <aside class="col-md-4">
        <section class="user_info">
          @include('shared._user_info', ['user' => Auth::user()])
        </section>
      </aside>
    </div>
  @else
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
  @endif
@stop
