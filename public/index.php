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
//


   if(!defined('BASEDIR'))
   {
     define('BASEDIR',dirname(__DIR__));
   }

   define('DS',DIRECTORY_SEPARATOR);

   define('PS',PATH_SEPARATOR);

   define('APPDIR',BASEDIR.DS.'app'.DS);

   define('SYSDIR',BASEDIR.DS.'system'.DS);


//--------------------------------------------------------------
   chdir(BASEDIR.DS);
//--------------------------------------------------------------
//
   require SYSDIR.'autoload.php';

   require_once SYSDIR.'Engine/Kernel.php';
//
//--------------------------------------------------------------
