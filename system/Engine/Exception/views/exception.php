<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <title>Error Page</title>
    <style media="screen">
      <?php include_once 'assets/css/style.css'; ?>
    </style>
</head>
<body>
    <br>
    <div class="container-fluid">
      <div class="panel panel-danger">
        <table class="table table-hover table-responsive table-inverse">
          <tr>
            <td class="bg-info"><strong>Code:</strong></td>
            <td><?php echo @$e->getCode(); ?></td>
          </tr>
          <tr>
            <td class="bg-info"><strong>File:</strong></td>
            <td style="color:#550ef0;font-weight:bold"><?php echo @$e->getFile();?></td>
          </tr>
          <tr>
            <td class="bg-info"><strong>Line:</strong></td>
            <td style="color:rgb(213, 18, 9)"><?php echo @$e->getLine(); ?></td>
          </tr>
          <tr>
            <td class="bg-info"><strong>Message:</strong></td>
            <td style="background:rgb(179, 56, 130);color:#fff;font-weight:bold">
              <?php echo @$e->getMessage(); ?>
            </td>
          </tr>
        </table>
      </div>
    </div>
    <div class="container-fluid">
      <button id="toggle" style="border-radius:0" class="btn btn-primary pull-right">Advanced</button>
      <div class="clearfix"></div>
    </div><br><br>
    <div class="clearfix"></div>
    <div class="container-fluid" id="advanced" style="display:none">
    <?php foreach ($e->getTrace() as $t): ?>
      <div class="panel panel-info">
        <table class="table table-hover  table-responsive">
          <thead>
            <th>Name</th>
            <th>Value</th>
          </thead>
          <tbody>
        <?php foreach ($t as $key => $value): ?>
          <?php if ($key == 'args') { continue; } ?>
          <?php if (!is_array($value)): ?>
            <tr>
              <td><strong><?php print_r(ucfirst($key)) ?></strong></td>
              <td><?php htmlspecialchars(print_r($value)) ?></td>
            </tr>
          <?php else: ?>
            <tr>
              <td class="bg-info"><strong><?php echo ucfirst($key) ?></strong></td>
              <td>
                <table class="table table-hover table-responsive">
                  <?php if (isset($value[0])): $i =0; ?>
                    <?php if (is_array($value[0])): ?>
                      <?php foreach ($value[0] as $k => $v): $i++; ?>
                        <tr>
                          <td><strong><?php print_r(ucfirst($k)) ?></strong></td>
                          <td><?php htmlspecialchars(print_r($v)) ?></td>
                        </tr>
                        <?php if ($i == 5) {  break; } ?>
                      <?php endforeach ?>
                    <?php else: ?>
                      <tr>
                        <td><?php htmlspecialchars(print_r($value[0])) ?></td>
                      </tr>
                    <?php endif ?>
                  <?php endif ?>
                </table>
              </td>
            </tr>
          <?php endif ?>
        <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endforeach; ?>
    </div>
    <script type="text/javascript">
      var button  = document.getElementById('toggle');
      var div     = document.getElementById('advanced');
      button.onclick = function() {
        if(div.style.display == 'none') {
          div.style.display = 'block';
        } else {
          div.style.display = 'none';
        }
      };
    </script>
</body>
</html>
