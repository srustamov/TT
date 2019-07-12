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
use System\Facades\Config;
use System\Facades\Hash;
use System\Libraries\Arr;
use System\Facades\DB;
use stdClass;

class Authentication
{
    private $message;


    private $driver;


    private $table;


    private $lockTime;


    private $maxAttempts;


    private $attemptDriver = 'session';


    private $hidden;


    private $user;




    public function __construct()
    {
        $config = Config::get('authentication');

        $this->attemptDriver = $config['attemptDriver'];
        $this->maxAttempts   = $config['maxAttempts'];
        $this->lockTime      = $config['lockTime'];
        $this->table         = $config['table'];
        $this->hidden        = $config['hidden'];
    }


    protected function beforeLogin($user):Bool
    {
        return true;
    }


    protected function afterLogin($user)
    {
        return true;
    }


    public function user($user = false)
    {
        if ($user && is_object($user)) {
            $this->user = (object) Arr::except((array) $user, $this->hidden);
        } else {
            if (!$this->user) {
                $authId = Session::get('AuthId');
    
                if ($authId) {
                    $result = DB::table($this->table)->where(['id' => $authId])->first();
    
                    $this->user = (object) Arr::except((array) $result, $this->hidden);
                }
            }
        }

        
        return $this->user;
    }



    public function attempt(array $data, $remember = false)
    {
        $this->setAttemptDriver();

        if (($attempts = $this->driver->getAttemptsCountOrFail())) {
            if ($attempts->count >= $this->maxAttempts) {
                if (($seconds =  $this->driver->getRemainingSecondsOrFail())) {
                    $this->message = $this->getLockMessage($seconds);
                    return false;
                }
            }
        }

        if (isset($data['password'])) {
            $password = $data['password'];

            unset($data['password']);
        } else {
            throw new \InvalidArgumentException("Auth password not found");
        }


        if (($result = DB::table($this->table)->where($data)->first())) {
            if (Hash::check($password, $result->password)) {
                $this->driver->deleteAttempt();

                if ($remember) {
                    $this->setRemember($result);
                }

                if ($this->beforeLogin($result)) {
                    $this->setSession($result);

                    $this->afterLogin($result);

                    return true;
                }

                return false;
            }
        }

        $this->driver->addAttempt();

        $remaining =  $this->maxAttempts - $this->driver->getAttemptsCountOrFail()->count;

        if ($remaining == 0) {
            $this->driver->startLockTime($this->lockTime);
        }
        $this->message = $this->getFailMessage($remaining);

        return false;
    }


    public function loginUser($user, $remember = false)
    {
        if (is_array($user) || is_object($user)) {
            try {
                $this->setSession($user);

                if ($remember) {
                    $this->setRemember($user);
                }
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }


    public function check()
    {
        if (Session::get('Login') === true) {
            if ($this->user() && is_object($this->user)) {
                $authId = Session::get('AuthId');

                
                if ($authId && $this->user->id === $authId) {
                    return true;
                }
            }
            return false;
        } else {
            if (($result = $this->remember())) {
                $this->beforeLogin($result);

                $this->setSession($result);

                return $this->afterLogin($result);
            }

            return false;
        }
    }


    public function guest()
    {
        return !$this->check();
    }


    public function remember()
    {
        if (Cookie::has('remember')) {
            $token = Cookie::get('remember');

            return DB::table($this->table)->where('remember_token', base64_decode($token))->first();
        }
        return false;
    }


    public function setRemember($user)
    {
        if ($user->remember_token) {
            Cookie::set('remember', base64_encode($user->remember_token), 3600 * 24 * 30);
        } else {
            $_token = hash_hmac('sha256', $user->email . $user->name, Config::get('app.key'));

            Cookie::set('remember', base64_encode($_token), 3600 * 24 * 30);

            DB::table($this->table)
                ->where('id', $user->id)
                ->update(['remember_token' => $_token]);
        }

        return $this;
    }


    public function getMessage()
    {
        return $this->message;
    }



    protected function setSession($user)
    {
        $this->user = $user;

        Session::set('AuthId', $user->id);

        Session::set('Login', true);
    }


    public function logoutUser()
    {
        $this->user = null;

        try {
            Session::delete('AuthId');

            Session::delete('Login');


            if (Cookie::has('remember')) {
                Cookie::forget('remember');
            }
        } catch (\Exception $e) {
            Session::destroy();
        }

        return $this;
    }


    public function redirect()
    {
        if (empty(func_get_args())) {
            return Redirect::instance();
        }
        return Redirect::to(...func_get_args());
    }


    protected function getFailMessage($remaining)
    {
        return Language::translate(
          'auth.incorrect',
          array(
        'remaining' => $remaining)
      );
    }


    protected function getLockMessage($seconds)
    {
        return Language::translate(
          'auth.many_attempts.text',
          array(
        'time' => $this->convertTime($seconds))
      );
    }


    protected function convertTime($seconds)
    {
        $minute = "";

        $second = "";

        if ($seconds >= 60) {
            $minute = (int) ($seconds/60);

            if ($minute > 1) {
                $minute = $minute." ".Language::translate('auth.many_attempts.minutes')." ";
            } else {
                $minute = $minute." ".Language::translate('auth.many_attempts.minute')." ";
            }

            if ($seconds%60 > 0) {
                $second = ($seconds%60);

                if ($second > 1) {
                    $second = $second." ".Language::translate('auth.many_attempts.seconds')." ";
                } else {
                    $second = $second." ".Language::translate('auth.many_attempts.second')." ";
                }
            }
        } else {
            $second = $seconds > 1
            ? $seconds." ".Language::translate('auth.many_attempts.seconds')." "
            : $seconds." ".Language::translate('auth.many_attempts.second')." ";
        }

        return $minute.$second;
    }


    protected function setAttemptDriver()
    {
        switch ($this->attemptDriver) {
            case 'session':
                $this->driver = new SessionAttemptDriver();
                break;
            case 'database':
                $this->driver = new DatabaseAttemptDriver();
                break;
            case 'redis':
                $this->driver = new RedisAttemptDriver();
                break;
            default:
                throw new \RuntimeException('Attempt Driver not found !');
                break;
        }
    }


    public function __get($key)
    {
        $user = $this->user();

        return $user->{$key} ?? false;
    }


    public function __set($key, $value)
    {
        $this->user->{$key} = $value;
        
        return $this;
    }


    public function __call($method, $args)
    {
        if ($this->check()) {
            return $this->user->{$method} ?? false;
        }
        return false;
    }


    public function __toString()
    {
        return $this->message;
    }
}
