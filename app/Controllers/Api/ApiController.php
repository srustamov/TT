<?php namespace App\Controllers\Api;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */

header('Content-Type:application/json');

use App\Controllers\Controller;
use System\Libraries\Request;
use App\Models\User;



class ApiController extends Controller
{


    public function response ($token)
    {
      if($user = User::where('api_token',$token)->first())
      {
        //
      }
      else
      {
        http_response_code(401);
        echo json_encode('Token invalid');
      }
      exit;
    }




}
