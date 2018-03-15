<?php namespace System\Libraries\Auth\Drivers;


use System\Facades\Redis as RDriver;
use System\Facades\Http;



class RedisAttemptDriver implements AttemptDriverInterface
{



  public function getAttemptsCountOrFail($guard)
  {
       if (($result = RDriver::get("AUTH_ATTEMPT_COUNT_{$guard}".Http::ip())))
       {
         return (object) array('count' => $result);
       }
       return false;
  }

  public function addAttempt($guard)
  {
      $count = $this->getAttemptsCountOrFail($guard);

      RDriver::setex("AUTH_ATTEMPT_COUNT_{$guard}".Http::ip(),60*60,
          $count ? $count->count+1 :1
        );

  }



  public function startLockTime($guard,$lock_time)
  {
    $expire = strtotime("+ {$lock_time} seconds");

    RDriver::expire("AUTH_ATTEMPT_COUNT_{$guard}".Http::ip(), $expire);

    RDriver::setex("AUTH_ATTEMPT_EXPIRE_{$guard}".Http::ip(),$expire,$expire);
  }


  public function deleteAttempt($guard)
  {
    RDriver::delete("AUTH_ATTEMPT_COUNT_{$guard}".Http::ip());
  }



  public function expireTimeOrFail($guard)
  {
    return RDriver::get("AUTH_ATTEMPT_EXPIRE_{$guard}".Http::ip());
  }


  public function getRemainingSecondsOrFail($guard)
  {
    if(($expireTime = $this->expireTimeOrFail($guard)))
    {
        $remaining_seconds = $expireTime - time();

        if($remaining_seconds > 0)
        {
            return $remaining_seconds;
        }
    }

    $this->deleteAttempt($guard);

    return false;
  }

}
