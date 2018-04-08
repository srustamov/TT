<?php namespace System\Libraries\Auth;
/**
 * @package TT
 * @author Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage  Library
 * @category  Authentication
 */

use System\Libraries\Auth\Drivers\SessionAttemptDriver;
use System\Libraries\Auth\Drivers\DatabaseAttemptDriver;
use System\Libraries\Auth\Drivers\RedisAttemptDriver;
use System\Facades\Language;
use System\Facades\Redirect;
use System\Facades\Session;
use System\Facades\Cookie;
use System\Facades\Load;
use System\Facades\DB;


class Authentication
{


    private $config;


    private $message;


    private $guard  = 'user';


    private $driver;


    private $table;


    private $lock_time;


    private $max_attempts;


    private $hidden;




    function __construct()
    {
        $this->config = Load::class('config')->get('authentication.guards');

        foreach ($this->config as $guard => $config)
        {
            $this->max_attempts[$guard] = $config['max_attempts'];
            $this->lock_time[$guard]    = $config['lock_time'];
            $this->table[$guard]        = $config['table'];
            $this->hidden[$guard]       = $config['hidden'];
        }
    }


    protected function beforeLogin($guard,$user)
    {
        //
    }


    protected function afterLogin($guard,$user):Bool
    {
        return true;
    }


    public function guard($guard)
    {
        $this->guard = $guard;

        return $this;
    }


    public function getGuard()
    {
        return $this->guard;
    }


    public function attempt(array $data, $remember = false)
    {
        $this->setAttemptDriver();

        if (($attempts = $this->driver[$this->guard]->getAttemptsCountOrFail($this->guard)))
        {
            if ($attempts->count >= $this->max_attempts[$this->guard])
            {
                if (($seconds =  $this->driver[$this->guard]->getRemainingSecondsOrFail($this->guard)))
                {
                    $this->message = $this->getLockMessage($seconds);
                    return false;
                }
            }
        }

        if(isset($data['password']))
        {
           $password = $data['password'];

           unset($data['password']);
        }
        else
        {
          throw new \InvalidArgumentException("Auth password not found");
        }


        if (($result = DB::table($this->table[$this->guard])->where($data)->first()))
        {

            if (password_verify($password, $result->password))
            {
                $this->driver[$this->guard]->deleteAttempt($this->guard);

                if ($remember)
                {
                    $this->setRemember($result);
                }

                $this->beforeLogin($this->guard, $result);

                $this->setSession($result);

                return $this->afterLogin($this->guard, $result);

            }
        }

        $this->driver[$this->guard]->addAttempt($this->guard);

        $remaining =  $this->max_attempts[$this->guard] - $this->driver[$this->guard]->getAttemptsCountOrFail($this->guard)->count;

        if ($remaining == 0)
        {
            $this->driver[$this->guard]->startLockTime($this->guard, $this->lock_time[$this->guard]);
        }
        $this->message = $this->getFailMessage($remaining);

        return false;
    }


    public function login($user, $remember = false)
    {
        if (is_array($user) || is_object($user))
        {
            try
            {
                $this->setSession($user);

                if ($remember)
                {
                    $this->setRemember($user);
                }
                return true;
            }
            catch (\Exception $e)
            {
                return false;
            }
        }
        return false;
    }


    public function check()
    {
        if (Session::get($this->guard.'_login') === true)
        {
            return true;
        }
        else
        {
            if (($result = $this->remember($this->guard)))
            {
                $this->beforeLogin($this->guard, $result);

                $this->setSession($result);

                return $this->afterLogin($this->guard, $result);

            }

            return false;
        }
    }


    public function guest()
    {
        return !$this->check();
    }


    public function remember($guard)
    {
        if (Cookie::has('remember_'.$guard))
        {
            $_token = Cookie::get('remember_'.$guard);

            return DB::table($this->table[$this->guard])->where('remember_token', base64_decode($_token))->first();
        }
        return false;
    }


    public function setRemember($user)
    {
        if ($user->remember_token)
        {
            Cookie::set('remember_' . $this->guard, base64_encode($user->remember_token), 3600 * 24 * 30);
        }
        else
        {
            $_token = hash_hmac('sha256', $user->email . $user->name, Load::class('config')->get('app.key'));

            Cookie::set('remember_'.$this->guard, base64_encode($_token), 3600 * 24 * 30);

            DB::table($this->config[$this->guard]['table'])->where('id', $user->id)->update(['remember_token' => $_token]);
        }

        return $this;
    }


    public function getMessage()
    {
        return $this->message;
    }


    protected function setSession($user)
    {
        $user = (array) $user;

        $set_data = [];

        foreach ($user as $key => $value)
        {
            if (array_search($key, $this->hidden[$this->guard]) !== false)
            {
                continue;
            }

            $set_data[$this->guard.'_'.$key] = $value;
        }

        $set_data[ $this->guard . '_login' ] = true;

        Session::setArray($set_data)->regenerate();

    }


    public function logout()
    {
        try
        {
            Session::delete(function ($session)
            {
                $data    = array();

                foreach (array_keys($session->all()) as $key => $value)
                {
                    if (preg_match("/".$this->guard."_(.*)/", $value))
                    {
                        array_push($data, $value);
                    }
                }
                return $data;
            })->regenerate();


            if (Cookie::has('remember_'.$this->guard))
            {
                Cookie::forget('remember_'.$this->guard);
            }
        }
        catch (\Exception $e)
        {
            Session::destroy();
        }

        return $this;
    }


    public function redirect()
    {
        return Redirect::to(...func_get_args());
    }


    protected function getFailMessage($remaining)
    {
      return Language::translate('auth.incorrect',array('remaining' => $remaining));
    }


    protected function getLockMessage($seconds)
    {
      return Language::translate('auth.many_attempts.text',array(
        'time' => $this->convertTime($seconds))
      );
    }


    protected function convertTime($seconds)
    {
        $minute = "";

        $second = "";

        if ($seconds >= 60)
        {
            $minute = (int) ($seconds/60);

            if ($minute > 1)
            {
                $minute = $minute." ".Language::translate('auth.many_attempts.minutes')." ";
            }
            else
            {
                $minute = $minute." ".Language::translate('auth.many_attempts.minute')." ";
            }

            if ($seconds%60 > 0)
            {
                $second = ($seconds%60);

                if ($second > 1)
                {
                    $second = $second." ".Language::translate('auth.many_attempts.seconds')." ";
                }
                else
                {
                    $second = $second." ".Language::translate('auth.many_attempts.second')." ";
                }
            }
        }
        else
        {
            $second = $seconds > 1
            ? $seconds." ".Language::translate('auth.many_attempts.seconds')." "
            : $seconds." ".Language::translate('auth.many_attempts.second')." ";
        }

        return $minute.$second;
    }


    protected function setAttemptDriver()
    {
        foreach ($this->config as $guard => $config)
        {
            switch ($config['attempts_driver'])
            {
              case 'session':
                $this->driver[$guard] = new SessionAttemptDriver();
                break;
              case 'database':
                $this->driver[$guard] = new DatabaseAttemptDriver();
                break;
              case 'redis':
                $this->driver[$guard] = new RedisAttemptDriver();
                break;
              default:
                $this->driver[$guard] = new SessionAttemptDriver();
                break;
            }
        }
    }


    public function __get($key)
    {
        return Session::get($this->guard.'_'.$key);
    }


    public function __set($key, $value)
    {
        Session::set($this->guard.'_'.$key, $value);
    }


    public function __call($method, $args)
    {
        $guard = $args[ 0 ] ?? $this->guard;

        if ($this->check($guard))
        {
            return Session::get($guard . '_' . $method);
        }

        return false;
    }


    public function __toString()
    {
      return $this->message;
    }
}
