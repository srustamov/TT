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
use System\Facades\Session;
use System\Facades\Cookie;
use System\Facades\Config;
use System\Facades\Hash;
use System\Libraries\Database\Model;


class Authentication
{
    protected $message;

    protected $throttle;

    /**@var Drivers\AttemptDriverInterface */
    protected $driver;


    protected $lockTime;


    protected $maxAttempts;


    protected $attemptDriverName;


    protected $hidden;


    protected $passwordName;


    protected $guard = 'default';

    /**@var Model*/
    protected $user;

    /**@var Model*/
    protected $model;


    public function __construct()
    {
        $guards = Config::get('authentication.guards');

        foreach ($guards as $guard => $config) {

            if(class_exists($config['model'])) {
                $this->model[$guard] = new $config['model']();
                if(!($this->model[$guard] instanceof Model)) {
                    throw new RuntimeException('Authentication model not instance Database\Model');
                }
            } else {
                throw new RuntimeException('Authentication model['.$config['model'].'] not found');
            }

            $this->throttle[$guard] = $config['throttle']['enable'] ?? false;

            $this->passwordName[$guard] = $config['password_name'] ?? 'password';

            if($this->throttle[$guard]) {
                $this->attemptDriverName[$guard] = $config['throttle']['driver'];
                $this->maxAttempts[$guard] = $config['throttle']['max_attempts'];
                $this->lockTime[$guard] = $config['throttle']['lock_time'];
            }

            $this->hidden[$guard] = $config['hidden'];
        }

    }


    public function guard(string $guard = null)
    {
        if ($guard !== null) {
            $this->guard = $guard;
            return $this;
        }
        return $this->guard;
    }


    protected function beforeLogin(Model $user,$guard): Bool
    {
        return true;
    }


    protected function afterLogin(Model $user,$guard)
    {
        //return true;
    }


    /**
     * @param Model $user
     * @param string $guard
     * @return object
     */
    public function user(Model $user = null, $guard = null)
    {
        $guard = $guard ?? $this->guard;

        if($user !== null) {
            $this->user[$guard] = $user;
        }

        if (!$this->user[$guard]) {
            if ($authId = Session::get(md5($guard).'-id')) {
                $user = $this->model[$guard]->find($authId);
                $this->user[$guard] = $user;
            }
        }

        if($this->user[$guard]) {
            foreach ($this->hidden[$guard] as $key) {
                unset($this->user[$guard][$key]);
            }
        }

        return $this->user[$guard];
    }


    protected function getPasswordName(): string
    {
        return $this->passwordName[$this->guard];
    }


    /**
     * @param array $data
     * @param bool $remember
     * @return bool
     * @throws Exception
     */
    public function attempt(array $data, $remember = false): bool
    {
        if($this->throttle[$this->guard]) {
            $this->setAttemptDriver();

            if (
                ($attempts = $this->driver[$this->guard]->getAttemptsCountOrFail()) &&
                $attempts->count >= $this->maxAttempts &&
                $seconds = $this->driver[$this->guard]->getRemainingSecondsOrFail()
            ) {
                $this->message[$this->guard] = $this->getLockMessage($seconds);
                return false;
            }
        }

        if (isset($data[$this->getPasswordName()])) {
            $password = $data[$this->getPasswordName()];

            unset($data[$this->getPasswordName()]);
        } else {
            throw new InvalidArgumentException('Auth ' . $this->getPasswordName() . ' not found');
        }

        if ($user = $this->model[$this->guard]->find($data)) {
            if (Hash::check($password, $user->password)) {
                if($this->throttle[$this->guard]) {
                    $this->driver[$this->guard]->deleteAttempt();
                }
                if ($remember) {
                    $this->setRemember($user);
                }
                if ($this->beforeLogin($user,$this->guard)) {
                    $this->setSession($user);
                    $this->afterLogin($user,$this->guard);
                    return true;
                }
                return false;
            }
        }

        if($this->throttle[$this->guard]) {
            $this->driver[$this->guard]->increment();
            $remaining = $this->maxAttempts[$this->guard] - $this->driver[$this->guard]->getAttemptsCountOrFail()->count;
            if ($remaining === 0) {
                $this->driver[$this->guard]->startLockTime($this->lockTime[$this->guard]);
            }
        }

        $this->message[$this->guard] = $this->getFailMessage($remaining ?? null);

        return false;
    }



    /**
     * @return bool
     */
    public function check(): bool
    {
        if($this->user[$this->guard] instanceof  Model) {
            return true;
        }
        if (Session::get(md5($this->guard).'-login') === true) {
            if ($this->user()) {
                $authId = Session::get(md5($this->guard).'-id');
                if ($authId && $this->user[$this->guard]->id === $authId) {
                    return true;
                }
            }
            return false;
        }

        if ($user = $this->remember()) {
            if ($this->beforeLogin($user,$this->guard)) {
                $this->setSession($user);
                $this->user[$this->guard] = $user;
                return true;
            }
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
     * @return bool|Model
     */
    public function remember()
    {
        if (Cookie::has(md5($this->guard).'-remember')) {
            $token = Cookie::get(md5($this->guard).'-remember');
            return $this->model[$this->guard]->find(['remember_token' => base64_decode($token)]);
        }
        return false;
    }


    /**
     * @param $user
     * @return $this
     */
    public function setRemember(Model $user): self
    {
        if ($user->remember_token) {
            Cookie::set(md5($this->guard).'-remember', base64_encode($user->remember_token), 3600 * 24 * 30);
        } else {
            $token = hash_hmac('sha256', $user->email . $user->name, Config::get('app.key'));

            Cookie::set(md5($this->guard).'-remember', base64_encode($token), 3600 * 24 * 30);

            $user->remember_token = $token;

            $user->save();
        }

        return $this;
    }


    public function getMessage()
    {
        return $this->message[$this->guard];
    }


    protected function setSession(Model $user)
    {
        Session::set(md5($this->guard).'-id', $user->id);
        Session::set(md5($this->guard).'-login', true);
        return $this;
    }


    public function logoutUser()
    {
        $this->user[$this->guard] = null;
        try {
            Session::delete(md5($this->guard).'-id');
            Session::delete(md5($this->guard).'-login');
            if (Cookie::has(md5($this->guard).'-remember')) {
                Cookie::forget(md5($this->guard).'-remember');
            }
        } catch (Exception $e) {
            Session::destroy();
        }
        return $this;
    }



    /**
     * @param $remaining
     * @return mixed
     * @throws Exception
     */
    protected function getFailMessage($remaining = null)
    {
        $message =  lang('auth.incorrect');
        if($remaining !== null) {
            $message .= lang('auth.remaining',array('remaining' => $remaining));
        }
        return $message;
    }


    /**
     * @param $seconds
     * @return mixed
     * @throws Exception
     */
    protected function getLockMessage($seconds)
    {
        return lang(
            'auth.many_attempts.text',
            array(
                'time' => $this->convertTime($seconds))
        );
    }


    /**
     * @param $seconds
     * @return string
     * @throws Exception
     */
    protected function convertTime($seconds): string
    {
        $minute = '';

        $second = '';

        if ($seconds >= 60) {
            $m = (int)($seconds / 60);

            $minute .= sprintf(' %s', lang('auth.many_attempts.minute' . ($m > 1 ? 's' : ''))) . ' ';

            if ($seconds % 60 > 0) {
                $s = ($seconds % 60);

                $second .= sprintf(' %s', lang('auth.many_attempts.second' . ($s > 1 ? 's' : ''))) . ' ';
            }
        } else {
            $second .= sprintf(' %s', lang('auth.many_attempts.second' . ($seconds > 1 ? 's' : ''))) . ' ';
        }

        return $minute . $second;
    }


    protected function setAttemptDriver(): void
    {
        switch ($this->attemptDriver[$this->guard]) {
            case 'session':
                $this->driver[$this->guard] = new Drivers\SessionAttemptDriver($this->guard);
                break;
            case 'database':
                $this->driver[$this->guard] = new Drivers\DatabaseAttemptDriver($this->guard);
                break;
            case 'redis':
                $this->driver[$this->guard] = new Drivers\RedisAttemptDriver($this->guard);
                break;
            default:
                throw new RuntimeException('Attempt Driver not found !');
                break;
        }
    }


    public function __get($key)
    {
        return $this->user()->{$key} ?? false;
    }

    public function __isset($name)
    {
        return true;
    }

    public function __set($key, $value)
    {
        $this->user()->{$key} = $value;

        return $this;
    }


    public function __call($method, $args)
    {
        if ($this->check()) {
            return $this->user()->{$method} ?? false;
        }
        return false;
    }


    public function __toString()
    {
        return $this->message[$this->guard] ?? '';
    }
}
