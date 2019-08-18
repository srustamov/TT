<?php


namespace App\Middleware;

use System\Engine\Http\Request;

class TrimString
{
    protected $except = [

    ];


    protected $emptyConvertNull = false;

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
    public function trimString(Request $request): Request
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
    public function trim($value, $name)
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->trim($key, $item);
            }
        }

        if (in_array($name, $this->except, true)) {
            return $value;
        }

        if(is_string($value)) {

            if($this->emptyConvertNull) {
                return $this->emptyStringConvertNull(trim($value));
            }
            return trim($value);
        }
        return $value;
    }

    /**
     * @param string $string
     * @return string|null
     */
    public function emptyStringConvertNull(string $string)
    {
        return empty($string) ? null : $string;
    }
}
