<?php  namespace App\Controllers;


/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */

use App\Controllers\Controller;
use System\Engine\Http\Request;


class WelcomeController extends Controller
{


  public function index()
  {
    return view('welcome');
  }

}
