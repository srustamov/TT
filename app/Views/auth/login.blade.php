@extends('layouts.app')
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
    animation: pop 1s infinite;
    -webkit-animation:pop 1s infinite;
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

  @-webkit-keyframes pop {
    50%{
      zoom:103%
    }
    100%{
      zoom:100%;
    }
  }


  @keyframes pop {
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
      <div class="col-md-6 offset-md-3 mt-5">
        <div class="card shadow-sm">
          <div class="card-header">
            <h3 class="card-title">Login User</h3><br>
            @if(($register_message = Session::flash('register')))
              <div class="alert alert-success fade in show" role="alert">
                 <button type="button" class="close" onclick="this.parentNode.style.display = 'none';">
                 <span aria-hidden="true">×</span>
                 </button>
                 <strong>{{$register_message}}</strong>
              </div>
            @endif
            @if ($errors->has('auth'))
              <div class="alert  alert-success fade in" role="alert">
                 <button type="button" class="close" onclick="this.parentNode.style.display = 'none';">
                 <span aria-hidden="true">×</span>
                 </button>
                 <strong>{{$errors->first('auth')}}</strong>
             </div>
            @endif
          </div>
          <form action="{{url('auth/login')}}" method="post">
            @csrf
            <div class="card-body">
              <div class="form-group">
                <label for="">Email</label>
                <input type="text" class="form-control" name="email"  placeholder="Email" autocomplete=off required/>
                @if ($errors->has('email'))
                <strong class="text-white custom">
                  <span onclick="this.parentNode.style.display = 'none';">&times;</span>
                  {{$errors->first('email')}}
                </strong>
                @endif
              </div>
              <div class="form-group">
                <label for="">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Password" autocomplete=off required/>
                @if ($errors->has('password'))
                <strong class="text-white custom">
                  <span onclick="this.parentNode.style.display = 'none';">&times;</span>
                  {{$errors->first('password')}}
                </strong>
                @endif
              </div>
              <div class="form-group">
                <label style="line-height:30px;height:30px">
                  <span  style="vertical-align:middle"><input type="checkbox" name="remember"></span>
                  <span>Remember Me</span>
                </label>
              </div>
              <div class="form-group">
                @if ($errors->has('login'))
                  <strong class="text-danger">{{$errors->login}}</strong>
                @endif
              </div>
            </div>
            <div class="card-footer text-right">
              <button type="submit" class="btn btn-outline-primary">Log in</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('js')
<script type="text/javascript">
  document.querySelectorAll('input').forEach(function(input){
      input.addEventListener('click',function(e){
          if(this.nextSibling.nextSibling) {
            this.nextSibling.nextSibling.style.display = 'none';
          }
        });
  });
</script>
@endsection
