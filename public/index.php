<?php
/**
 * TT
 *
 * TT is a simple mvc application.
 *
 * @author 		Samir Rustamov <rustemovv96@gmail.com>
 * @version 	1
 * @copyright	2017
 * @link 		https://github.com/SamirRustamov/TT
 *
 */

//--------------------------------------------------------------
define('APP_START', microtime(true));
//--------------------------------------------------------------


require_once __DIR__.'/../vendor/autoload.php';



System\Engine\Kernel::start(realpath('../'));
