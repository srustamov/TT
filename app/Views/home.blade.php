@extends('layouts.app')
@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-8 offset-md-2 ">
      <div class="card  shadow-lg mt-5 rounded-0">
        <div class="card-header">
          <h3>Home Page</h3>
        </div>
        <div class="card-body">
          <h6 class="text-success">{{ Auth::check() ? 'Logged in' : 'Hello Guest' }}</h6>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection