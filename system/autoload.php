<?php

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */


/*
| -------------------------------------------
|  Autoload Classes
| -------------------------------------------
*/

$autoload_file = BASEDIR . DS . 'vendor/autoload.php';

if(!file_exists($autoload_file)): ?>
  <div style="color:red;font-size:20px">
    Vendor folder not found. Run
    '<b style="color:dodgerblue">composer install</b>' command on command line.
  </div>
<?php endif;

require $autoload_file;
