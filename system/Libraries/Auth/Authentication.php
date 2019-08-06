<?php namespace System\Libraries\Auth;

/**
 * @package TT
 * @author Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage  Library
 * @category  Authentication
 */

use Exception;
use RuntimeException;
use InvalidArgumentException;
use System\Facades\Language;
use System\Facades\Redirect;
use System\Facades\Session;
use System\Facades\Cookie;
use System\Facades\Config;
use System\Libraries\Arr;
use System\Facades\Hash;
use System\Facades\DB;


class Authentication
{
    protected $message;


    /**@var Drivers\AttemptDriverInterface*/
    protected $driver;


    protected $table;


    protected $lockTime;


    protected $maxAttempts;


    protected $attemptDriver = 'session';


    protected $hidden;


    protected $user;




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


    protected function afterLogin($user): bool
    {
        return true;
    }


    /**
     * @param bool $user
     * @return object
     */
    public function user($user = false)
    {
        if ($user && is_object($user)) {
            $this->user = (object) Arr::except((array) $user, $this->hidden);
        } else if (!$this->user) {
            $authId = Session::get('AuthId');

            if ($authId) {
                $result = DB::table($this->table)->where(['id' => $authId])->first();

                $this->user = (object) Arr::except((array) $result, $this->hidden);
            }
        }


        return $this->user;
    }


    /**
     * @param array $data
     * @param bool $remember
     * @return bool
     */
    public function attempt(array $data, $remember = false): bool
    {
        $this->setAttemptDriver();

        if (
            ($attempts = $this->driver->getAttemptsCountOrFail()) &&
             $attempts->count >= $this->maxAttempts &&
            $seconds = $this->driver->getRemainingSecondsOrFail()
        ) {
            $this->message = $this->getLockMessage($seconds);
            return false;
        }

        if (isset($data['password'])) {
            $password = $data['password'];

            unset($data['password']);
        } else {
            throw new InvalidArgumentException('Auth password not found');
        }


        if ($result = DB::table($this->table)->where($data)->first()) {
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

        $this->driver->increment();

        $remaining =  $this->maxAttempts - $this->driver->getAttemptsCountOrFail()->count;

        if ($remaining === 0) {
            $this->driver->startLockTime($this->lockTime);
        }
        $this->message = $this->getFailMessage($remaining);

        return false;
    }


    /**
     * @param $user
     * @param bool $remember
     * @return bool
     */
    public function loginUser($user, $remember = false): bool
    {
        if (is_array($user) || is_object($user)) {
            try {
                $this->setSession($user);

                if ($remember) {
                    $this->setRemember($user);
                }
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
        return false;
    }


    /**
     * @return bool
     */
    public function check():bool
    {
        if (Session::get('Login') === true) {
            if ($this->user() && is_object($this->user)) {
                $authId = Session::get('AuthId');


                if ($authId && $this->user->id === $authId) {
                    return true;
                }
            }
            return false;
        }

        if ($result = $this->remember()) {
            $this->beforeLogin($result);

            $this->setSession($result);

            return $this->afterLogin($result);
        }

        return false;
    }


    /**
     * @return bool
     */
    public function guest(): bool
    {
        return !$this->check();
    }


    /**
     * @return bool|Object
     */
    public function remember()
    {
        if (Cookie::has('remember')) {
            $token = Cookie::get('remember');

            return DB::table($this->table)->where('remember_token', base64_decode($token))->first();
        }
        return false;
    }


    /**
     * @param $user
     * @return $this
     */
    public function setRemember($user): self
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
        } catch (Exception $e) {
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


    /**
     * @param $seconds
     * @return string
     */
    protected function convertTime($seconds): string
    {
        $minute = '';

        $second = '';

        if ($seconds >= 60) {
            $m = (int) ($seconds/60);

            $minute .= sprintf(' %s',Language::translate('auth.many_attempts.minute'.($m > 1 ? 's' : ''))) . ' ';

            if ($seconds%60 > 0) {
                $s = ($seconds%60);

                $second .= sprintf(' %s',Language::translate('auth.many_attempts.second'.($s > 1 ? 's' : ''))) . ' ';
            }
        } else {
            $second .= sprintf(' %s',Language::translate('auth.many_attempts.second'.($seconds > 1 ? 's' : ''))) . ' ';
        }

        return $minute.$second;
    }


    protected function setAttemptDriver()
    {
        switch ($this->attemptDriver) {
            case 'session':
                $this->driver = new Drivers\SessionAttemptDriver();
                break;
            case 'database':
                $this->driver = new Drivers\DatabaseAttemptDriver();
                break;
            case 'redis':
                $this->driver = new Drivers\RedisAttemptDriver();
                break;
            default:
                throw new RuntimeException('Attempt Driver not found !');
                break;
        }
    }


    public function __get($key)
    {
        $user = $this->user();

        return $user->{$key} ?? false;
    }

    public function __isset($name)
    {
        return true;
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
