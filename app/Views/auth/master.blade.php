<!DOCTYPE html>
<html lang="{{lang()->locale()}}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    @yield('style')
  </head>
  <body>
    <nav class="navbar navbar-inverse navbar-static-top" role="navigation">
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
            <li @if(url()->request() == '/home') class="active" @endif><a href="/home">@lang('home.home')</a></li>
            @if (url()->current() == url('auth/login/'))
            <li><a href="/auth/register">@lang('home.register')</a></li>
            @elseif (url()->current() == url('auth/register/'))
            <li><a href="/auth/login">@lang('home.login')</a></li>
            @endif
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li @if(lang()->locale() == 'az') class="active" @endif><a href="/language/az" >az</a></li>
            <li @if(lang()->locale() == 'en') class="active" @endif><a href="/language/en">en</a></li>
            <li @if(lang()->locale() == 'tr') class="active" @endif><a href="/language/tr">tr</a></li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="content">
       @yield('content')
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    @benchmark_panel()
  </body>
</html>
