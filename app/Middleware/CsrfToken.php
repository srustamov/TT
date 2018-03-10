<?php  namespace App\Middleware;


use System\Engine\Http\Request;
use System\Facades\Cookie;

class CsrfToken
{



    public function handle(Request $request)
    {


        if ($this->isReading($request) || $this->isConsole() || $this->tokensMatch($request)) {
            $this->addCookie($request);
        } else {
          if(config('config.debug')) {
              throw new \Exception('VERIFY CSRF TOKEN FAILED');
          } else {
              abort(404);
          }
        }
    }


    protected function isReading(Request $request)
    {
        return in_array($request->method(), ['HEAD', 'GET', 'OPTIONS']);
    }


    protected function isConsole()
    {
        return inConsole();
    }


    protected function tokensMatch(Request $request)
    {
        $token = $this->getTokenRequest($request);

        return is_string($request->session('_token')) &&
               is_string($token) &&
               hash_equals($request->session('_token'), $token);
    }



    protected function getTokenRequest(Request $request)
    {
        $input    = $request->input()->_token;

        $response = $request->cookie('XSRF-TOKEN');

        if(
          !is_string($input) ||
          !is_string($response) ||
          empty(trim($input)) ||
          empty(trim($response))
        ) {
          return false;
        } elseif ($input !== $response) {
          return false;
        } else {
          return $input;
        }


    }


    protected function addCookie(Request $request)
    {
        $lifetime = config('session.lifetime');

        Cookie::set('XSRF-TOKEN', $request->session('_token'), $lifetime);

    }

}
