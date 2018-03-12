<?php namespace System\Libraries\Auth\Drivers;


use System\Libraries\Auth\Drivers\Attempt_Driver_Interface;
use System\Facades\Redis as RDriver;
use System\Facades\Http;



class Redis_Attempt_Driver implements Attempt_Driver_Interface
{



  public function getAttemptsCountOrFail($guard)
  {
       if (($result = RDriver::get("AUTH_ATTEMP_COUNT_{$guard}".Http::ip())))
       {
         return (object) array('count' => $result);
       }
       return false;
  }

  public function addAttempt($guard)
  {
      $count = $this->getAttemptsCountOrFail($guard);

      RDriver::setex("AUTH_ATTEMP_COUNT_{$guard}".Http::ip(),60*60,
          $count ? $count->count+1 :1
        );

  }



  public function startLockTime($guard,$lock_time)
  {
    $expire = strtotime("+ {$lock_time} seconds");

    RDriver::expire("AUTH_ATTEMP_COUNT_{$guard}".Http::ip(), $expire);

    RDriver::setex("AUTH_ATTEMP_EXPIRE_{$guard}".Http::ip(),$expire,$expire);
  }


  public function deleteAttempt($guard)
  {
    RDriver::delete("AUTH_ATTEMP_COUNT_{$guard}".Http::ip());
  }



  public function expireDateOrFail($guard)
  {
    return RDriver::get("AUTH_ATTEMP_EXPIRE_{$guard}".Http::ip());
  }


  public function getRemainingSecondsOrFail($guard)
  {
    if($expiredate = $this->expireDateOrFail($guard))
    {
        $remaining_seconds = $expiredate - time();
        if($remaining_seconds > 0)
        {
            return $remaining_seconds;
        }
    }

    $this->deleteAttempt($guard);

    return false;
  }

}
