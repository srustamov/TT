<?php namespace System\Libraries\Session;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Session
 */


use System\Libraries\Session\Drivers\SessionFileHandler;
use System\Libraries\Session\Drivers\SessionDBHandler;
use System\Libraries\Session\Drivers\SessionRedisHandler;
use System\Facades\OpenSsl;
//use System\Facades\Cookie;
use System\Engine\Load;
use ArrayAccess;
use Countable;

class Session implements ArrayAccess,Countable
{


    protected $config;

    protected $handler;


    public function start()
    {

      if (session_status() == PHP_SESSION_NONE)
      {
          $this->config = Load::class('config')->get('session');

          ini_set('session.cookie_httponly', $this->config['cookie']['http_only']);
          ini_set('session.use_only_cookies', $this->config['only_cookies']);
          ini_set('session.gc_maxlifetime', $this->config['lifetime']);
          ini_set('session.save_path', $this->config['files_location']);

          session_set_cookie_params(
              $this->config['lifetime'],
              $this->config['cookie']['path'],
              $this->config['cookie']['domain'],
              $this->config['cookie']['secure'],
              $this->config['cookie']['http_only']
          );

          session_name($this->config['cookie']['name']);

          switch ($this->config['driver'])
          {
              case 'database':
                  $this->handler = new SessionDBHandler($this->config['table']);
                  break;
              case 'redis':
                  $this->handler = new SessionRedisHandler();
                  break;
              default:
                  $this->handler = new SessionFileHandler();
                  break;
          }

          if (!is_null($this->handler))
          {
              session_set_save_handler($this->handler, true); 

              register_shutdown_function('session_write_close');
          }

            if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
                session_start([
                    'cache_limiter' => 'private',
                    'read_and_close' => true,
                    ]);
            } else {
                session_start();
            }
          

          $this->token();

      }
    }


    /**
     * @param $key
     * @param $value
     * @return mixed
     */

    public function set($key, $value)
    {
        if ($value instanceOf \Closure)
        {
            return $this->set($key, call_user_func($value, $this));
        }
        else
        {
            $_SESSION[ $key ] = $value;

            if ($this->config['regenerate'] === true)
            {
                $this->regenerate();
            }
        }
    }


    /**
     * @return string
     */

    private function token():String
    {
        if (!$this->has('_token'))
        {
            $token = base64_encode(OpenSsl::random(40));

            $this->set('_token', $token);
        }
        else
        {
            $token = $this->get('_token');
        }

        return $token;
    }


    public function prevUrl()
    {
      return $this->get('_prev_url');
    }


    /**
     * @param $key
     * @return bool
     */

    public function get($key, $default = false)
    {

      if(isset($_SESSION[ $key ]))
      {
        return $_SESSION[ $key ];
      }
      else
      {
        if($default instanceOf \Closure)
        {
          $default = call_user_func($default, $this);
        }

        return $_SESSION[ $key ] ?? $default;
      }


    }


    public function flash($key,$value = null)
    {
        if($value)
        {
            $_SESSION['_flash-data'][$key] = $value;
        }
        else
        {
            if (isset($_SESSION['_flash-data'][$key]))
            {
                $return = $_SESSION['_flash-data'][$key];

                unset($_SESSION['_flash-data'][$key]);

                if(empty($_SESSION['_flash-data']))
                {
                    unset($_SESSION['_flash-data']);
                }
                return $return;
            }
            return false;

        }
    }


    /**
     * @param array $data
     */
    public function setArray(array $data)
    {
        foreach ($data as $key => $value)
        {
            $this->set($key, $value);
        }

        return $this;
    }


    /**
     * @return array
     */
    public function all():array
    {
        return $_SESSION;
    }



    /**
     * @param $key
     * @return Bool
     */
    public function has($key):Bool
    {
        return isset($_SESSION[ $key ]);
    }


    /**
     * @param $key
     */

    public function delete($key)
    {
        if ($key instanceOf \Closure)
        {
            $this->delete(call_user_func($key, $this));
        }
        else
        {
            if (is_array($key))
            {
                foreach ($key as  $value)
                {
                    $this->delete($value);
                }
            }
            else
            {
                if (isset($_SESSION[ $key ]))
                {
                    unset($_SESSION[ $key ]);
                }
            }
        }

        return $this;
    }



    public function regenerate()
    {

        if($this->config['driver'] != 'file')
        {
            //$id = $this->handler->regenerate(session_id());

            //$this->destroy();
        }
        else
        {
            session_regenerate_id(true);
        }

        return $this;
    }



    public function __get($key)
    {
        return $this->get($key);
    }


    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }


    public function __isset($key)
    {
        return $this->has($key);
    }


    public function __call($method, $args)
    {
        $value = $args[0] ?? null;

        return is_null($value)
            ? $this->get($method)
            : $this->set($method, $value);
    }


    public function destroy()
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {

            $params = session_get_cookie_params();

            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
    }



    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet ( $offset )
    {
        return $this->get($offset);
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
    public function offsetSet ( $offset , $value )
    {
        $this->set($offset,$value);
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
    public function offsetUnset ( $offset )
    {
        $this->delete($offset);
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
    public function offsetExists ( $offset )
    {
        return $this->has($offset);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count ()
    {
        return count($this->all());
    }
}
