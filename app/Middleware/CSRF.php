<?php

namespace App\Middleware;

use RuntimeException;
use TT\Engine\Http\Request;
use TT\Facades\Cookie;
use TT\Facades\Config;
use Closure;


class CSRF
{
    private $except = [
        '/api/.*'
    ];



    public function handle(Request $request, Closure $next)
    {
        if (
            CONSOLE ||
            $this->isReading($request) ||
            $this->isExcept($request) ||
            $this->tokensMatch($request)
        ) {
            $this->addCookie($request);
        } else {
            throw new RuntimeException('VERIFY CSRF TOKEN FAILED');
        }

        return $next($request);
    }


    protected function isReading(Request $request): bool
    {
        return in_array($request->getMethod(), ['HEAD', 'GET', 'OPTIONS']);
    }


    protected function isExcept(Request $request): bool
    {
        if (!empty($this->except)) {
            $url = trim($request->url(), '/');

            foreach ($this->except as $key => $value) {
                $value = trim($value, '/');

                if (preg_match("#^$value$#", $url)) {
                    return true;
                }
            }
        }
        return false;
    }


    protected function tokensMatch(Request $request): bool
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
        }

        if ($input !== $response) {
            return false;
        }

        return $input;
    }


    protected function addCookie(Request $request)
    {
        if(!CONSOLE) {
            Cookie::set(
                'XSRF-TOKEN', 
                $request->session('_token'), 
                Config::get('session.lifetime')
            );
        }
    }
}
