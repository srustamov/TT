<?php namespace System\Libraries\Auth;

use System\Libraries\Auth\Drivers\Session_Attempt_Driver;
use System\Libraries\Auth\Drivers\Database_Attempt_Driver;
use System\Facades\Session as Auth_Session;
use System\Facades\Cookie as Auth_Cookie;
use System\Facades\DB as Auth_DB;


class Authentication
{

  private static $config;


  private static $message;


  private static $guard  = 'user';


  private $driver;


  private $table;


  private $lock_time;


  private $max_attempts;


  private $hidden;


  private $cookie;


  private $session;




  function __construct()
  {
    if(is_null(self::$config))
    {
      static::$config = config('authentication')['guards'];
    }

    foreach (static::$config as $guard => $config)
    {
      $this->max_attempts[$guard] = $config['max_attempts'];
      $this->lock_time[$guard]    = $config['lock_time'];
      $this->table[$guard]        = $config['table'];
      $this->hidden[$guard]       = $config['hidden'];
    }

  }



  protected function beforeLogin($guard,$login_user_data)
  {
    //
  }



  public function guard($guard)
  {
      static::$guard = $guard;
      return $this;
  }



  public  function getGuard()
  {
    return static::$guard;
  }




  public function attempt($data, $remember = false)
  {
    $this->setAttemptDriver();

    if($attempts = $this->driver[static::$guard]->getAttemptsCountOrFail(static::$guard))
    {
      if($attempts->count >= $this->max_attempts[static::$guard])
      {
        if($seconds =  $this->driver[static::$guard]->getRemainingSecondsOrFail(static::$guard))
        {
            static::$message = "You have been temporarily locked out! Please wait {$this->convertTime($seconds)}";
            return false;
        }
      }
    }

    $password = $data['password']; unset($data['password']);

    if($result = Auth_DB::table($this->table[static::$guard])->where($data)->first())
    {
      if(password_verify($password,$result->password))
      {
          $this->driver[static::$guard]->deleteAttempt(static::$guard);

          if($remember)
          {
              $this->setRemember($result);
          }

          $this->beforeLogin(static::$guard, $result);


          $this->setSession($result);

          return true;
      }

    }

    $this->driver[static::$guard]->addAttempt(static::$guard);

    $remaining =  $this->max_attempts[static::$guard] - $this->driver[static::$guard]->getAttemptsCountOrFail(static::$guard)->count;

    if($remaining == 0)
    {
      $this->driver[static::$guard]->startLockTime(static::$guard,$this->lock_time[static::$guard]);
    }
    static::$message = "Login or password incorrect! ".sprintf("%d attempts remaining !",$remaining);

    return false;


  }





  public function login($user,$remember = false)
  {
    if(is_array($user) || is_object($user))
    {
      try
      {
        $this->setSession($user);

        if($remember)
        {
          $this->setRemember($user);
        }
        return true;
      }
      catch(\Exception $e)
      {
        return false;
      }

    }
    return false;
  }




  public function check()
  {
      if(Auth_Session::get(static::$guard.'_login') === true)
      {
          return true;
      }
      else
      {
        if($result = $this->remember(static::$guard))
        {
            $this->beforeLogin(static::$guard, $result);

            $this->setSession($result);

            return true;
        }
        return false;
      }

  }



  public function guest()
  {
      return !self::check();
  }


  public function remember($guard)
  {
    if($_token = Auth_Cookie::get('remember_'.$guard))
    {
        return Auth_DB::table($this->table[static::$guard])->where('remember_token',base64_decode($_token))->first();
    }
    return false;
  }


  public function setRemember($user)
  {
    if($_token = $user->remember_token)
    {
      Auth_Cookie::set('remember_' . static::$guard, base64_encode($_token), 3600 * 24 * 30);
    }
    else
    {
      $_token = hash_hmac('sha256',$user->email . $user->name,self::ENC_KEY);
      Auth_Cookie::set('remember_'.static::$guard, base64_encode($_token), 3600 * 24 * 30);
      Auth_DB::table(static::$config[static::$guard]['table'])->set(['remember_token' => $_token])->where('id',$user->id)->update();
    }

    return $this;
  }



  public function getMessage()
  {
    return static::$message;
  }


  private function setSession($guard_data)
  {
    $guard_data = (array) $guard_data;

    $set_data = [];

    foreach ($guard_data as $key => $value)
    {
        if(array_search($key,$this->hidden[static::$guard]) !== false)
        {
          continue;
        }
        $set_data[static::$guard.'_'.$key] = $value;
    }

    $set_data[ static::$guard . '_login' ] = true;

    Auth_Session::setArray($set_data);

  }




  public function logout()
  {
    try
    {
      Auth_Session::delete(function ($session)
      {

          $data    = array();

          foreach (array_keys($session->all()) as $key => $value)
          {
            if (preg_match("/".static::$guard."_(.*)/", $value))
            {
              array_push($data, $value);
            }
          }
          return $data;
      });

      if(Auth_Cookie::has('remember_'.static::$guard))
      {
        Auth_Cookie::forget('remember_'.static::$guard);
      }

    }
    catch(\Exception $e)
    {
      Auth_Session::destroy();
    }

    return $this;
  }


  public function redirect(...$args)
  {
    return redirect(...$args);
  }



  private function convertTime($seconds)
  {
    $minute = ""; $second = "";
    if($seconds >= 60) {
      $minute = (int) ($seconds/60);
      if ($minute > 1) {
        $minute = $minute." minutes ";
      } else {
        $minute = $minute." minute ";
      }
      if($seconds%60 > 0) {
        $second = ($seconds%60);
        if ($second > 1) {
          $second = $second." seconds ";
        } else {
          $second = $second." second ";
        }
      }
    }
    else {
      $second = $seconds > 1 ? $seconds." seconds " : $seconds." second ";
    }

    return $minute.$second;
  }


  private function setAttemptDriver()
  {
    foreach (static::$config as $guard => $config)
    {
      switch ($config['attempts_driver'])
      {
        case 'session':
          $this->driver[$guard] = new Session_Attempt_Driver();
          break;
        case 'database':
          $this->driver[$guard] = new Database_Attempt_Driver();
          break;
        default:
          $this->driver[$guard] = new Session_Attempt_Driver();
          break;
      }
    }
  }


  public function __get($key)
  {
    return Auth_Session::get(static::$guard.'_'.$key);
  }

  public function __set($key,$value)
  {
    Auth_Session::set(static::$guard.'_'.$key,$value);
  }



  public static function __callStatic($method, $args)
  {
      return ( new static )->__call($method, $args);
  }




  public function __call($method, $args)
  {
    $guard = $args[ 0 ] ?? static::$guard;

    if (self::check($guard))
    {
        return Auth_Session::get($guard . '_' . $method);
    }
    else
    {
        return false;
    }
  }



}
