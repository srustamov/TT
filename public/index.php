<?php
/**
 * @TT
 *
 * TT is a simple and fastest mvc application.
 *
 * @author 		Samir Rustamov <rustemovv96@gmail.com>
 * @version 	1
 * @copyright	2017
 * @link 		https://github.com/srustamov/TT
 *
 */

header('X-Powered-By:TT Framework');
//--------------------------------------------------
// Defined application started time
//--------------------------------------------------
define('APP_START', microtime(true));



//------------------------------------------
// Load Composer Autoload file
//------------------------------------------
require __DIR__.'/../vendor/autoload.php';


//------------------------------------------
// Application Bootstrapping and Routing
//------------------------------------------

$TT = new App\Kernel(dirname(__DIR__));


$response = $TT->bootstrap()->routing();




//------------------------------------------
// Application Benchmark panel view
//------------------------------------------

$response->appendContent($TT->benchmark(microtime(true)));



//------------------------------------------
// Response content and headers send
//------------------------------------------

$response->send();
