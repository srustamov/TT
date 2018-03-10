@extends('layouts.app')
@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Home Page</h3>
          </div>
          <div class="panel-body">
            <h3>{{ Auth::check() ? 'Logged in' : 'Hello Guest' }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
