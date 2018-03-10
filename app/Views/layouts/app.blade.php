<!DOCTYPE html>
<html lang="{{lang()->locale()}}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <title>Home</title>
    @css('/css/bootstrap.css')
    @yield('style')
  </head>
  <body>
    <nav class="navbar navbar-static-top navbar-default" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">
            <strong class="text-primary">{{setting('APP_NAME','TT')}}</strong>
          </a>
        </div>
        <div class="collapse navbar-collapse" id="navbar">
          <ul class="nav navbar-nav navbar-menu-link">
            <li>
              <a href="{{url('/home')}}">@lang('home.home')</a>
            </li>
            @if (Auth::guest())
              <li><a href="{{url('/auth/register')}}">@lang('home.register')</a></li>
              <li><a href="{{url('/auth/login')}}">@lang('home.login')</a></li>
            @endif
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li>
              <a class="lang" href="/language/az">az</a>
            </li>
            <li>
              <a class="lang" href="/language/en">en</a>
            </li>
            <li>
              <a class="lang" href="/language/tr">tr</a>
            </li>
            @if (Auth::check())
            <li class="btn-group">
              <button style="border-radius:0;min-height:50px" class="btn btn-success">
               {{ Auth::name() }}
             </button>
              <button onclick="window.location.href='/auth/logout'"
                  style="border-radius:0;min-height:50px"
                  role="button" class="btn btn-danger">
                Logout
              </button>
            </li>
            @endif
          </ul>
        </div>
      </div>
    </nav>
    @yield('content')
    <script type="text/javascript">
      var locale   = '{{lang()->locale()}}';

      var elements = document.querySelectorAll('a.lang');

      elements.forEach(function (a) {
          if(a.attributes.href.nodeValue.substr(-2) == locale) {
              a.parentElement.classList.add('active');
          }
      });

      var menus = document.querySelectorAll('ul.navbar-menu-link li a');

      var currentUrl = window.location.href;

      menus.forEach(function(menu){
        if(menu.attributes.href.nodeValue == currentUrl) {
          menu.parentElement.classList.add('active');
        }
      });


    </script>
  </body>
</html>
