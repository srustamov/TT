<?php  namespace App\Controllers;


/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */

use App\Controllers\Controller;
use TT\Engine\Http\Request;


class WelcomeController extends Controller
{


  public function index()
  {
    return file_get_contents(
      app_path('Views/welcome.html')
    );
  }

}
