<!DOCTYPE html>
<html lang="{{lang()->locale()}}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  </head>
  <body>
    <nav class="navbar navbar-static-top navbar-inverse" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">{{setting('APP_NAME','TT')}}</a>
        </div>
        <div class="collapse navbar-collapse" id="navbar">
          <ul class="nav navbar-nav">
            <li @if(Url::request() == '/home') class="active" @endif><a href="/home">@lang('home.home')</a></li>
          @if (Auth::guest())
            <li><a href="/auth/login">@lang('home.login')</a></li>
            <li><a href="/auth/register">@lang('home.register')</a></li>
          @endif
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li @if(lang()->locale() == 'az') class="active" @endif><a href="/language/az" >az</a></li>
            <li @if(lang()->locale() == 'en') class="active" @endif><a href="/language/en">en</a></li>
            <li @if(lang()->locale() == 'tr') class="active" @endif><a href="/language/tr">tr</a></li>
            @if (Auth::check())
              <li class="dropdown">
              <a href="javascript:void();" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
              <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
               {{ Auth::name()}} <b class="caret"></b>
              </a>
              <ul class="dropdown-menu  animated fadeInDown  pull-right">
                <li>
                <a href="/auth/logout" title="logout">
                <span class="glyphicon glyphicon-log-out pull-right" aria-hidden="true"></span>
                 Logout
                </a>
                </li>
              </ul>
            </li>
            @endif
          </ul>
        </div>
      </div>
    </nav>
    <div class="container">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">Home Page</h3>
            </div>
            <div class="panel-body">
              @if (Auth::check())
                <h3>Logged in</h3>
              @else
                <h3>Hello Guest</h3>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
    {!! benchmark_panel() !!}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  </body>
</html>
