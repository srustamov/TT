@extends('auth.master')
@section('style')
<style media="screen">
  .custom{
    background-color: rgb(179, 51, 87);
    color:white;
    text-align: center;
    display: block;
    position: relative;
    min-height: 30px;
    line-height: 30px;
    animation: 'pop' 1s infinite;
    -webkit-animation:'pop' 1s infinite;
    font-weight:normal;

  }
  .custom > span{
    position: absolute;
    right: 7px;
    cursor: pointer;
    font-size: 13px;
    box-sizing: border-box;
    z-index: 1;
    font-weight: bold;

  }

  @-webkit-keyframes "pop" {
    50%{
      zoom:103%
    }
    100%{
      zoom:100%;
    }
  }


  @keyframes "pop" {
    50%{
      zoom:103%
    }
    100%{
      zoom:100%;
    }
  }

  input , .btn {
    border-radius: 0 !important;
  }
</style>
@endsection
@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Login User</h3><br>
            @if(isset($success->register_success))
              <div class="alert  alert-success fade in" role="alert">
                 <button type="button" class="close" onclick="this.parentNode.style.display = 'none';">
                 <span aria-hidden="true">×</span>
                 </button>
                 <strong>{{$success->register_success}}</strong>
             </div>
            @endif
          </div>
          <form action="/auth/login" method="post">
            {!! csrf_field() !!}
            <div class="panel-body">
              <div class="form-group">
                <label for="">Email</label>
                <input type="text" class="form-control" name="email"  placeholder="Email" required>
                @if (isset($error->email[0]))
                <strong class="text-danger custom">
                  <span onclick="this.parentNode.style.display = 'none';">x</span>
                  {{$error->email[0]}}
                </strong>
                @endif
              </div>
              <div class="form-group">
                <label for="">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Password">
                @if (isset($error->password[0]))
                <strong class="text-danger custom">
                  <span onclick="this.parentNode.style.display = 'none';">x</span>
                  {{$error->password[0]}}
                </strong>
                @endif
              </div>
              <div class="form-group">
                <label for="" style="line-height:30px;height:30px">
                  <span  style="vertical-align:middle"><input type="checkbox" name="remember"></span>
                  <span>Remember Me</span>
                </label>
              </div>
              <div class="form-group">
                @if (isset($error->login_incorrect))
                  <strong class="text-danger">{{$error->login_incorrect}}</strong>
                @endif
              </div>
            </div>
            <div class="panel-footer text-right">
              <button type="submit" class="btn btn-primary">Log in</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
