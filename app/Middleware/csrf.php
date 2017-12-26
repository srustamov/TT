<?php  namespace App\Middleware;


use System\Libraries\Request;
use Exception as CsrfException;


class Csrf
{

    public function handle(Request $request,$guard = null)
    {
        if ($request->method() == 'POST') {
            if(!csrf_check()) {
                throw new CsrfException("Verify csrf token failed");
            }
        }
        return;
    }

}
