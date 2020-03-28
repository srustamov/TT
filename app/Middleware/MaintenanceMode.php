<?php

namespace App\Middleware;

use Closure;
use function preg_match;
use TT\Engine\Http\Request;
use TT\Engine\Http\Response;

class MaintenanceMode
{
    protected $excepts = [
        /*
            '/',
            '/login',
            '/home/?(.*)/any-url'
         */];

    protected $options = [
        'message' => 'Service Unavailable',
        'allowed' => [
            //'127.0.0.1'
        ]
    ];


    /**
     * @param $request
     * @param Closure $next
     * @return Response|mixed
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->check($request) || $this->checkExcepts($request)) {
            return $next($request);
        }

        $response = $next($request);

        $response->make(view('framework.maintenance', [
            'message' => $this->options['message']
        ])->getContent(), 503)->send();

        $request->app()->end();
    }


    /**
     * @param $request
     * @return bool
     */
    public function check($request): bool
    {
        if ($this->checkAllowed($request)) {
            return false;
        }
        return true;
    }


    /**
     * @param Request $request
     * @return bool
     */
    public function checkAllowed(Request $request): bool
    {
        if (empty($this->options['allowed'])) {
            return false;
        }

        $clientIp = $request->ip();

        foreach ($this->options['allowed'] as $ip) {
            if ($clientIp === $ip) {
                return true;
            }
        }

        return false;
    }


    /**
     * @param Request $request
     * @return bool
     */
    public function checkExcepts(Request $request): bool
    {
        if (empty($this->excepts)) {
            return false;
        }

        $url = $request->url();

        foreach ($this->excepts as $except) {
            if (preg_match("#^$except$#", $url)) {
                return true;
            }
        }

        return false;
    }
}
