<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <title>{!!$message ?? 'Service Unavailable' !!}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Poppins, sans-serif;
        }

        div.code {
            text-align: center;
            font-size: 10em;
            color: #455a64;
            font-weight: bold;
            line-height: 210px;
            letter-spacing: normal;
        }

        div.message {
            text-align: center;
            color: #455a64;
            font-size: 21px;
            line-height: 30px;
        }


        div.btn a {
            font-size: 14px;
            line-height: 17.5px;
            text-decoration: none;
            background-color: #f2f5f7;
            color: #455a64;
            font-weight: bold;
            border-radius: 60px;
            padding: 8px 15px;
            box-shadow: rgba(66, 165, 245, 0.14) 0px 2px 2px 0px, rgba(66, 165, 245, 0.2) 0px 3px 1px -2px, rgba(66, 165, 245, 0.12) 0px 1px 5px 0px;

        }
    </style>
</head>

<body>
    <div class="code">503</div>
    <div class="message">{!!$message ?? '' !!}</div>
</body>

</html>