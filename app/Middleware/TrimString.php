<?php


namespace App\Middleware;


use System\Engine\Http\Request;

class TrimString
{

    protected $except = [

    ];

    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        return $next($this->trimString($request));
    }


    /**
     * @param Request $request
     * @return Request
     */
    public function trimString(Request $request)
    {
        $request->map([$this,'trim']);
        $request->query->map([$this,'trim']);
        $request->input->map([$this,'trim']);
        return $request;
    }

    /**
     * @param $name
     * @param $value
     * @return array|string
     */
    public function trim($name, $value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->trim($key,$item);
            }
        }

        if(in_array($name,$this->except,true)) {
            return $value;
        }

        return is_string($value) ? trim($value) :$value;
    }

}
