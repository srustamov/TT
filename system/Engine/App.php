<?php 

namespace System\Engine;

/**
 * Application class
 *
 * @author Samir Rustamov <rustemovv96@gmail.com>
 * @link   https://github.com/srustamov/tt
 */

use ArrayAccess;
use Exception;
use System\Facades\Route;
use System\Facades\Config;
use System\Facades\Http;
use System\Libraries\Benchmark;
use System\Engine\Http\Middleware;
use System\Engine\Http\Request;
use System\Engine\Http\Response;

class App implements ArrayAccess
{
    public const VERSION = '1.2.0';

    public static $classes = [];

    protected $bootstrapping = false;

    protected $middleware = [];

    protected $routeMiddleware = [];

    public $paths = [
        'base' => 'app',
        'public' => 'public',
        'storage' => 'storage',
        'lang' => 'lang',
        'configs' => 'app/Config',
        'envFile' => '.config',
        'envCacheFile' => 'storage/system/config',
        'configsCacheFile' => 'storage/system/configs.php',
        'routesCacheFile' => 'storage/system/routes.php',
    ];


    protected static $instance;


    /**
     * App constructor.
     * Set application base path
     *
     * @param null $basePath
     */
    public function __construct($basePath = null)
    {
        if (!defined('CONSOLE')) {
            define('CONSOLE', PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg');
        }

        if ($basePath === null) {
            $this->paths['base'] = dirname(__DIR__, 2);
        } else {
            $this->paths['base'] = rtrim($basePath, DIRECTORY_SEPARATOR);
        }

        chdir($this->paths['base']);

        static::$instance = $this;
    }

    public function version(): string
    {
        return static::VERSION;
    }

    /**
     * Application bootstrapping
     *
     * @return $this
     * @throws Exception
     */
    public function bootstrap(): self
    {
        if (!$this->bootstrapping) {
            $this->setPublicPath();

            self::register('request', new Request($this));

            $this->callImportantClasses();

            self::register('app', $this);

            $this->setAliases();

            $this->registerMiddleware($this->middleware);

            $this->setLocale();

            $this->bootstrapping = true;
        }

        return $this;
    }

    protected function callImportantClasses()
    {
        (new LoadEnvVariables($this))->handle();
        (new PrepareConfigs($this))->handle();
        (new RegisterExceptionHandler($this))->handle();
    }


    /**
     * @param array $middleware_array
     * @throws Exception
     */
    protected function registerMiddleware(array $middleware_array): void
    {
        foreach ($middleware_array as $middleware) {
            Middleware::init($middleware, true);
        }
    }

    /**
     *  Application aliases setting
     */
    protected function setAliases(): void
    {
        $aliases = Config::get('aliases', []);

        $aliases['App'] = get_class($this);

        foreach ($aliases as $key => $value) {
            class_alias('\\' . $value, $key);
        }
    }

    /**
     * Set application locale and timezone
     *
     * @return void
     */
    protected function setLocale(): void
    {
        setlocale(LC_ALL, Config::get('datetime.setLocale'));

        date_default_timezone_set(Config::get('datetime.time_zone', 'UTC'));
    }


    /**
     * Application Routing
     *
     * @return Response
     */
    public function routing(): Response
    {
        return Route::execute($this, $this->routeMiddleware);
    }


    /**
     * Show Application Benchmark Panel in development mode
     *
     * @param $finish
     * @return string|mixed
     */
    public function benchmark($finish)
    {
        if (!(CONSOLE || !Config::get('app.debug') || Http::isAjax())) {
            $benchmark = new Benchmark($this);
            self::register('benchmark', $benchmark);
            return $benchmark->table($finish);
        }
    }

    /**
     * Create application public path
     *
     * @param String|null $path
     */
    public function setPublicPath(String $path = null): void
    {
        if ($path !== null) {
            $this->paths['public'] = $path;
        } elseif (isset($_SERVER['SCRIPT_FILENAME']) && !empty($_SERVER['SCRIPT_FILENAME'])) {
            $parts = explode('/', $_SERVER['SCRIPT_FILENAME']);

            array_pop($parts);

            $this->paths['public'] = implode('/', $parts);
        } else {
            $this->paths['public'] = $this->paths['base'] . DIRECTORY_SEPARATOR . 'public';
        }
    }

    /**
     * @return string
     */
    public function envFile(): string
    {
        return $this->path($this->paths['envFile']);
    }

    /**
     * @param string $path
     * @return string
     */
    public function publicPath($path = ''): string
    {
        return $this->paths['public'] . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $path
     * @return string
     */
    public function path($path = ''): string
    {
        return $this->paths['base'] . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $path
     * @return string
     */
    public function storagePath($path = ''): string
    {
        return $this->path($this->paths['storage'] . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR));
    }

    public function configsPath($path = ''): string
    {
        return $this->path($this->paths['configs'] . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR));
    }

    public function appPath($path = ''): string
    {
        return $this->paths['base']
            . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR
            . ltrim($path, DIRECTORY_SEPARATOR);
    }


    public function langPath($path = ''): string
    {
        return $this->path($this->paths['lang'] . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR));
    }

    public function configsCacheFile(String $file = null): string
    {
        if ($file !== null) {
            $this->paths['configsCacheFile'] = $file;
        }
        return $this->path($this->paths['configsCacheFile']);
    }

    public function routesCacheFile(String $file = null): string
    {
        if ($file !== null) {
            $this->paths['routesCacheFile'] = $file;
        }
        return $this->path($this->paths['routesCacheFile']);
    }

    public function envCacheFile(String $file = null): string
    {
        if ($file !== null) {
            $this->paths['envCacheFile'] = $file;
        }

        return $this->path($this->paths['envCacheFile']);
    }


    public function classes(String $name = null, Bool $isValue = false)
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
            'redis' => 'System\Libraries\Redis',
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

        if ($name === null) {
            return $classes;
        }

        if (!$isValue) {
            return $classes[strtolower($name)] ?? false;
        }

        return array_search($name, $classes, true);
    }

    /**
     * @param string $class
     * @param mixed ...$args
     * @return mixed
     * @throws \RuntimeException
     */
    public static function get(string $class, ...$args)
    {
        if (isset(static::$classes[$class])) {
            return static::$classes[$class];
        }

        if ($instance = self::instance()->classes($class)) {
            if (method_exists($instance, '__construct')) {
                $args = Reflections::classMethodParameters($instance, '__construct', $args);
            }

            static::$classes[$class] = new $instance(...$args);

            return static::$classes[$class];
        }

        if (strpos($class, '\\')) {
            if ($instance = self::instance()->classes($class, true)) {
                return static::get($instance, ...$args);
            }

            $instance = new $class(...Reflections::classMethodParameters($class, '__construct', $args));

            static::$classes[$class] = $instance;

            unset($instance);

            return static::$classes[$class];
        }
        throw new \RuntimeException('Class not found [' . $class . ']');
    }


    public static function register($className, $object)
    {
        if ($object instanceof \Closure) {
            static::register($className, $object());
        } elseif (is_string($object)) {
            static::$classes[$className] = new $object();
        } elseif (is_object($object)) {
            static::$classes[$className] = $object;
        }
    }


    /**
     * @param $object
     * @param $className
     * @return bool
     * @throws \Exception
     */
    public static function isInstance($object, $className): bool
    {
        $instance = static::get($className);

        return ($object instanceof $instance);
    }

    /**
     * @return mixed
     */
    public static function instance()
    {
        return self::$instance;
    }


    public static function end(): void
    {
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        exit();
    }


    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     * @throws Exception
     */
    public function offsetGet($offset)
    {
        return self::get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value): void
    {
        self::register($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset): void
    {
        unset($this[$offset]);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, self::$classes);
    }
}
