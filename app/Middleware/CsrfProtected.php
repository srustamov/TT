<?php  namespace App\Middleware;


use System\Engine\Http\Request;
use System\Facades\Cookie;
use System\Facades\Load;

class CsrfProtected
{



    private $except = [
      //'/api/.*'
    ];



    public function handle(Request $request,\Closure $next)
    {

        if ($this->isReading($request) || $this->isConsole() || $this->isExcept() || $this->tokensMatch($request))
        {
            $this->addCookie($request);
        }
        else
        {
          throw new \Exception('VERIFY CSRF TOKEN FAILED');
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


    protected function isExcept()
    {
      if(!empty($this->except)) {

        $url = trim(Load::class('url')->request(),'/');

        foreach ($this->except as $key => $value)
        {
          $value = trim($value,'/');

          if(preg_match("#^$value$#",$url))
          {
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
        $input    = $request->input('_token');
        
        $response = $request->cookie('XSRF-TOKEN');

        if(
          !is_string($input) ||
          !is_string($response) ||
          empty(trim($input)) ||
          empty(trim($response))
        )
        {
          return false;
        }
        elseif ($input !== $response)
        {
          return false;
        }
        else
        {
          return $input;
        }


    }


    protected function addCookie(Request $request)
    {
        $lifetime = Load::config('session.lifetime');

        Cookie::set('XSRF-TOKEN', $request->session('_token'), $lifetime);

    }

}
