<?php namespace System\Engine;


/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */



class Load
{


    protected static $classes = [];


    public static function class( String $class,...$args)
    {

        if(isset(static::$classes[$class]))
        {
            return static::$classes[$class];
        }
        else
        {

            if (($instance = static::applicationClasses($class)))
            {
                if (method_exists($instance,'__construct'))
                {
                    $args = Reflections::classMethodParameters($instance,'__construct',$args);
                }

                static::$classes[$class] = new $instance(...$args);

                return static::$classes[$class];
            }
            else
            {
                if(strpos($class,'\\'))
                {

                    if(($instance = static::applicationClasses($class,true)))
                    {
                        return static::class($instance,...$args);
                    }
                    else
                    {
                        $instance = new $class(...Reflections::classMethodParameters($class,'__construct', $args));

                        static::$classes[$class] = $instance;

                        unset($instance);

                        return static::$classes[$class];
                    }

                }
            }

        }
        throw new \Exception('Class not found ['.$class.']');
    }


    public static function register($className,$object)
    {
        if(static::applicationClasses($className)) {
            throw new \Exception("Class name already exists in application classes");
        }

        if($object instanceof \Closure) {
            static::set($className,call_user_func($object));
        } elseif (is_string($object)) {
            static::$classes[$className] = new $object();
        } elseif (is_object($object)) {
            static::$classes[$className] = $object;
        }

    }


    public static function isInstance($object,$className)
    {
        $instance = static::class($className);

        return ($object instanceOf $instance);
    }


    public static function applicationClasses(String $name = null,Bool $isValue = false)
    {

        $classes = array(
            'array' => 'System\Libraries\Arr',
            'authentication' => 'System\Libraries\Auth\Authentication',
            'cache' => 'System\Libraries\Cache\Cache',
            'console' => 'System\Engine\Cli\Console',
            'cookie' => 'System\Libraries\Cookie',
            'database' => 'System\Libraries\Database\Database',
            'email' => 'System\Libraries\Mail\Email',
            'file' => 'System\Libraries\File',
            'hash' => 'System\Libraries\Hash',
            'html' => 'System\Libraries\Html',
            'http' => 'System\Libraries\Http',
            'input' => 'System\Libraries\Input',
            'lang' => 'System\Libraries\Language',
            'language' => 'System\Libraries\Language',
            'middleware' => 'System\Engine\Http\Middleware',
            'openssl' => 'System\Libraries\Encrypt\OpenSsl',
            'redirect' => 'System\Libraries\Redirect',
            'redis' => 'System\Libraries\RedisFactory',
            'request' => 'System\Engine\Http\Request',
            'response' => 'System\Engine\Http\Response',
            'route' => 'System\Engine\Http\Routing\Route',
            'session' => 'System\Libraries\Session\Session',
            'str' => 'System\Libraries\Str',
            'string' => 'System\Libraries\Str',
            'storage' => 'System\Libraries\Storage',
            'url' => 'System\Libraries\Url',
            'validator' => 'System\Libraries\Validator',
            'view' => 'System\Libraries\View\View',
        );

        if(is_null($name)) {
            return $classes;
        }

        if(!$isValue) {
            return $classes[strtolower($name)] ?? false;
        } else {
            return array_search($name,$classes);
        }
    }




}
