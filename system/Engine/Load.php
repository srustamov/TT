<?php namespace System\Engine;


/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */



class Load
{


    protected static $classes = [];


    public function class( String $class,Array $args = [])
    {
        if(strtoupper($class) == 'LOAD') {
            return $this;
        }

        if(isset(static::$classes[$class]))
        {
            return static::$classes[$class];
        }
        else
        {

            if (($instance = $this->applicationClasses($class)))
            {
                if (method_exists($instance,'__construct'))
                {
                    $args = $this->getReflectionMethodParameters($instance,$args);
                }

                static::$classes[$class] = new $instance(...$args);

                return static::$classes[$class];
            }
            else
            {
                if(strpos($class,'\\'))
                {

                    if(($instance = $this->applicationClasses($class,true)))
                    {
                        return $this->class($instance,$args);
                    }
                    else
                    {
                        $instance = new $class(...$this->getReflectionMethodParameters($class, $args));

                        static::$classes[$class] = $instance;

                        unset($instance);

                        return static::$classes[$class];
                    }

                }
            }

        }
        throw new \Exception('Class not found ['.$class.']');
    }


    public function set($className,$object)
    {
        if($this->applicationClasses($className)) {
            throw new \Exception("Class name already exists in application classes");
        }

        if($object instanceof \Closure) {
            $this->set($className,call_user_func($object));
        } elseif (is_string($object)) {
            static::$classes[$className] = new $object();
        } elseif (is_object($object)) {
            static::$classes[$className] = $object;
        }

    }


    public function instance($object,$className)
    {
        $instance = $this->class($className);

        return ($object instanceOf $instance);
    }


    public function file( String $file)
    {
        $file = str_replace(['/','\\'], DS, trim($file));

        if (file_exists($file))
        {
            return require_once $file;
        }
        else
        {
            throw new \Exception("File not found. Path: [ $file ]");
        }
    }


    private function getReflectionMethodParameters($class_name,$args)
    {
        $reflection = new \ReflectionMethod($class_name, '__construct');

        foreach ($reflection->getParameters() as $num => $param)
        {

            if ($param->getClass())
            {
                $class = $param->getClass()->name;

                if(($instance = $this->applicationClasses($class)))
                {
                    $args[$num] = $this->class($instance);
                }
                else
                {
                    $args[$num] = new $class();
                }

            }
        }
        return $args;
    }


    public function applicationClasses(String $name = null,Bool $isValue = false)
    {

        $classes = array(
            'array' => 'System\Libraries\Arr',
            'authentication' => 'System\Libraries\Auth\Authentication',
            'cache' => 'System\Libraries\Cache\Cache',
            'console' => 'System\Engine\Cli\Console',
            'config' => 'System\Engine\Config',
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
            'load' => 'System\Core\Load',
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


    public function settingVariables()
    {
        $settingsFile = path ( 'storage/system/settings' );


        if (!file_exists ( $settingsFile ) || filemtime ( $settingsFile ) < filemtime ( path ( '.settings' ) ))
        {

            $_auto_detect = ini_get ( 'auto_detect_line_endings' );

            ini_set ( 'auto_detect_line_endings' , 1 );

            $lines = file ( path ( '.settings' ) , FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

            ini_set ( 'auto_detect_line_endings' , $_auto_detect );

            $_settings = [];

            foreach ($lines as $line)
            {
                $line = trim ( $line );

                if (isset( $line[ 0 ] ) && $line[ 0 ] === '#')
                {
                    continue;
                }

                if (strpos ( $line , '=' ) !== false)
                {
                    list( $name , $value ) = array_map ( 'trim' , explode ( '=' , $line , 2 ) );

                    $name = str_replace(['\'','"'],'',$name);

                    if (preg_match ( '/\s+/' , $value ) > 0)
                    {
                        throw new \RuntimeException( "setting variable value containing spaces must be surrounded by quotes" );
                    }

                    if (strtolower ( $value ) == 'true')
                    {
                        $value = true;
                    }
                    elseif (strtolower ( $value ) == 'false')
                    {
                        $value = false;
                    }


                    $_settings[ $name ] = $value;
                }
            }


            foreach ($_settings as $key => $value)
            {

                if (strpos ( $value , '$' ) !== false)
                {
                    $value = preg_replace_callback ( '/\${([a-zA-Z0-9_]+)}/' ,
                        function ( $m ) use ( $_settings )
                        {
                            if (isset( $_settings[ $m[ 1 ] ] ))
                            {
                                return $_settings[ $m[ 1 ] ];
                            }
                            else
                            {
                                return ${"$m[1]"} ?? '${' . $m[ 1 ] . '}';
                            }
                        } ,
                        $value
                    );
                }

                if(function_exists('putenv'))
                {
                    putenv("$key=$value");
                }
                if (function_exists('apache_setenv'))
                {
                    apache_setenv($key,$value);
                }

                $_ENV[ $key ] = $value;
            }
            file_put_contents ( path ( 'storage/system/settings' ) , serialize ( $_ENV ) );
        }
        else
        {

            $_settings = (array) unserialize ( file_get_contents ( path ( 'storage/system/settings' ) ) );

            foreach ($_settings as $key => $value)
            {
                if(function_exists('putenv'))
                {
                    putenv("$key=$value");
                }
                if (function_exists('apache_setenv'))
                {
                    apache_setenv($key,$value);
                }
                $_ENV[ $key ] = $value;
            }
        }
    }


}
