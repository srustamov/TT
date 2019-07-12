<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Error Page</title>
    <style>
        <?php include_once __DIR__.'/style.css';?>
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <br>
            <table class="table table-bordered table-inverse">
                <tr style="background-color: #97310e;color:#fff;font-size: 17px">
                    <td>Message</td>
                    <td><?php echo $e->getMessage(); ?></td>
                </tr>
                <tr>
                    <td>File</td>
                    <td><?php echo $e->getFile(); ?></td>
                </tr>
                <tr>
                    <td>Line</td>
                    <td><?php echo $e->getLine(); ?></td>
                </tr>
                <tr>
                    <td>Code</td>
                    <td><?php echo $e->getCode(); ?></td>
                </tr>
            </table>
        </div>
        <?php if (isset($e->xdebug_message)): ?>
        <hr><div style="text-align: center"><h3>Xdebug Message</h3></p><hr>
        <table class="table">
            <?php echo $e->xdebug_message ?>
        </table>
        <?php endif ?>
    </div>
</body>
</html>
