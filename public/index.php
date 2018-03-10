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

//--------------------------------------------------
// Defined application started time
//--------------------------------------------------
define('APP_START', microtime(true));



//------------------------------------------
//   Load Composer Autoload file
//------------------------------------------
require __DIR__.'/../vendor/autoload.php';



//------------------------------------------
//   Application Bootstrapping and Routing
//------------------------------------------

System\Engine\Kernel::start(realpath('../'));



//------------------------------------------
//   Application Benchmark panel view
//------------------------------------------
echo benchmark();
