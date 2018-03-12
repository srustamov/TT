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
            <h3 class="panel-title">Login User </h3>
          </div>
          <form action="{{url('auth/register')}}" method="post" id="register-form" onsubmit="return false;">
            @csrf
            <div class="panel-body">
              <div class="form-group">
                <label for="">Name</label>
                <input type="text" class="form-control" name="name"  placeholder="Name" required>
                @if ($errors->has('name'))
                  <p class="text-danger">{{$errors->first('name')}}</p>
                @endif
              </div>
              <div class="form-group">
                <label for="">Email</label>
                <input type="text" class="form-control" name="email"  placeholder="Email" required>
                @if ($errors->has('email'))
                  <p class="text-danger">{{$errors->first('email')}}</p>
                @endif
              </div>
              <div class="form-group">
                <label for="">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                @if ($errors->has('password'))
                  <p class="text-danger">{{$errors->first('password')}}</p>
                @endif
              </div>
              <div class="form-group">
                <label for="">Password Configuration</label>
                <input type="text" class="form-control" id="password_configuration" placeholder="Password Configuration" required>
              </div>
            </div>
            <div class="panel-footer text-right">
              <button type="submit" onclick="formSubmit(event)" class="btn btn-primary "name="button">Register</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('js')
<script type="text/javascript">

    function formSubmit(e)
    {

      let password = document.getElementById('password');

      let password_configuration = document.getElementById('password_configuration');


      if(trim(password.value) !== '' && trim(password_configuration.value) !== '')
      {
        if (password.value === password_configuration.value)
        {
          document.getElementById('register-form').removeAttribute('onsubmit').submit();
        }
      }

      let oldBorder = password.style.border;

      password.style.border = '1px solid red';
      password_configuration.style.border = '1px solid red';
      setTimeout(function(){
        password.style.border = oldBorder;
        password_configuration.style.border = oldBorder;
      },2000);


      return false;


    }

    function trim(str)
    {
      return str.replace(/\s+/,'');
    }


  </script>
@endsection
