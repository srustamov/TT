<?php  namespace App\Middleware;


use System\Engine\Http\Request;


class Csrf
{

    public function handle(Request $request)
    {
        if ($request->server('request_method') == 'POST')
        {
            if(!csrf_check()) {
                if(config('debug')) {
                    show_error('Verify Csrf Token Failed');
                } else {
                    abort(404);
                }
            }
        }
    }

}
