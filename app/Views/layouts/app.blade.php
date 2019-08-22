<!DOCTYPE html>
<html lang="{{lang()->locale()}}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <title>Home</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:300,400,600">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    @yield('style')
  </head>
  <body>
      <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-lg">
          <a class="navbar-brand" href="/">
              <strong class="text-primary">{{config('app.name','TT')}}</strong>
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
              <li class="nav-item">
                  <a class="nav-link" href="{{route('home')}}">@lang('home.home')</a>
              </li>
              @guest
              <li class="nav-item"><a class="nav-link" href="{{route('register')}}">@lang('home.register')</a></li>
              <li class="nav-item"><a class="nav-link" href="{{route('login')}}">@lang('home.login')</a></li>
              @endguest
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="nav-item">
                  <a class="nav-link text-uppercase font-weight-bold lang" href="{{route('lang',['lang' => 'az'])}}">az</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link text-uppercase font-weight-bold lang" href="{{route('lang',['lang' => 'en'])}}">en</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link text-uppercase font-weight-bold lang" href="{{route('lang',['lang' => 'tr'])}}">tr</a>
                </li>
                @auth
                <li class="nav-item btn-group">
                  <button class="btn btn-success rounded-0 shadow-sm">
                   {{ ucfirst(Auth::name()) }}
                 </button>
                  <button onclick="window.location.href='{{route('logout')}}'" class="btn btn-danger rounded-0 shadow-sm">
                    Logout
                  </button>
                </li>
                @endauth
              </ul>
          </div>
        </nav>
    @yield('content')
    <script type="text/javascript">

      let locale   = document.getElementsByTagName('html')[0].getAttribute('lang');

      let elements = document.querySelectorAll('a.lang');

      elements.forEach(function (a) {
          if(a.attributes.href.nodeValue.replace(/^\/|\/$/g, '').substr(-2) === locale) {
              a.parentElement.classList.add('active');
          }
      });

      let menus = document.querySelectorAll('ul.navbar-nav.mr-auto li a');

      let currentUrl = window.location.href;

      menus.forEach(function(menu){
        if(menu.attributes.href.nodeValue === currentUrl) {
          menu.parentElement.classList.add('active');
        }
      });
    </script>
    @yield('js')
  </body>
</html>{{-- <!--Load Time (seconds) : {{(new \TT\Libraries\Benchmark(\TT\Engine\App::getInstance()))->loadTime()}} --> --}}
