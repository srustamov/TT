@extends('layouts.app')
@section('style')
  <style media="screen">
    input , .btn {
      border-radius: 0 !important;
    }
  </style>
@endsection
@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Login User</h3>
          </div>
          <form action="/auth/register" method="post">
            @csrf
            <div class="panel-body">
              <div class="form-group">
                <label for="">Name</label>
                <input type="text" class="form-control" name="name"  placeholder="Name" required>
                <p class="text-danger">{{$error->name[0] ?? ''}}</p>
              </div>
              <div class="form-group">
                <label for="">Email</label>
                <input type="text" class="form-control" name="email"  placeholder="Email" required>
                <p class="text-danger">{{$error->email[0] ?? ''}}</p>
              </div>
              <div class="form-group">
                <label for="">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
                <p class="text-danger">{{$error->password[0] ?? ''}}</p>
              </div>
              <div class="form-group">
                <label for="">Password Configuration</label>
                <input type="text" class="form-control" name="password_configuration" placeholder="Password Configuration" required>
                <p class="text-danger">{{$error->password_configuration[0] ?? ''}}</p>
              </div>
            </div>
            <div class="panel-footer text-right">
              <button type="submit" class="btn btn-primary "name="button">Register</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
