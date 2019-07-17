<?php  namespace App\Middleware;

use System\Engine\Http\Request;
use System\Facades\Cookie;
use System\Facades\Config;
use System\Facades\Url;

class CsrfProtected
{
    private $except = [
      '/api/.*'
    ];



    public function handle(Request $request, \Closure $next)
    {
        if ($this->isReading($request) || CONSOLE || $this->isExcept() || $this->tokensMatch($request)) {
            $this->addCookie($request);
        } else {
            throw new \Exception('VERIFY CSRF TOKEN FAILED');
        }

        return $next($request);
    }


    protected function isReading(Request $request)
    {
        return in_array($request->method('GET'), ['HEAD', 'GET', 'OPTIONS']);
    }


    protected function isExcept()
    {
        if (!empty($this->except)) {
            $url = trim(Url::request(), '/');

            foreach ($this->except as $key => $value) {
                $value = trim($value, '/');

                if (preg_match("#^$value$#", $url)) {
                    return true;
                }
            }
        }
        return false;
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
        $input = $request->get('_token');

        $response = Cookie::get('XSRF-TOKEN');

        if (
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
        Cookie::set('XSRF-TOKEN', $request->session('_token'), Config::get('session.lifetime'));
    }
}
