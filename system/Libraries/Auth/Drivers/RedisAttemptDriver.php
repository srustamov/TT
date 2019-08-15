<?php namespace System\Libraries\Auth\Drivers;

use System\Facades\Redis as R;
use System\Facades\Http;

class RedisAttemptDriver implements AttemptDriverInterface
{
    public function getAttemptsCountOrFail()
    {
        if (($result = R::get("AUTH_ATTEMPT_COUNT".Http::ip()))) {
            return (object) array('count' => $result);
        }
        return false;
    }

    public function increment()
    {
        $count = $this->getAttemptsCountOrFail();

        R::setex(
            "AUTH_ATTEMPT_COUNT".Http::ip(),
            60*60,
            $count ? $count->count+1 :1
        );
    }



    public function startLockTime($lockTime)
    {
        $expire = strtotime("+ {$lockTime} seconds");

        R::expire("AUTH_ATTEMPT_COUNT".Http::ip(), $expire);

        R::setex("AUTH_ATTEMPT_EXPIRE".Http::ip(), $expire, $expire);
    }


    public function deleteAttempt()
    {
        R::delete("AUTH_ATTEMPT_COUNT".Http::ip());
        R::delete("AUTH_ATTEMPT_EXPIRE".Http::ip());
    }



    public function expireTimeOrFail()
    {
        return R::get("AUTH_ATTEMPT_EXPIRE".Http::ip());
    }


    public function getRemainingSecondsOrFail()
    {
        if (($expireTime = $this->expireTimeOrFail())) {
            $remaining_seconds = $expireTime - time();

            if ($remaining_seconds > 0) {
                return $remaining_seconds;
            }
        }

        $this->deleteAttempt();

        return false;
    }
}
