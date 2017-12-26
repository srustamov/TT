<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <title>{!!config('config.app_name') !!}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: rgb(255, 255, 255);
        }

        #container {
            text-align: center;
            padding-top: 17vw;
            font-weight: bold;
        }

        #welcome {
            color: #000;
            margin-bottom: 3vw;
            font-size: 5em;
            display: block;
            opacity: 0.4;
        }

        span#about {
            display: block;
            margin: 3vw auto;
            color: rgb(70, 9, 148);
            text-transform: uppercase;
        }

        span a {
            text-decoration: none;
            color: rgb(45, 55, 48);
            cursor: pointer;
        }
    </style>
</head>
<body>
<div id="container">
    <span id="welcome">{!! config('config.app_name') !!}</span>
</div>



@nocache
  if (isset($error->auth)):
    echo '
    <script type="text/javascript">
      alert("'.$error->auth.'");
    </script>
    ';
  endif;
@endnocache
</body>
</html>
