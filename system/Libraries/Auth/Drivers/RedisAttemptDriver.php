<?php namespace System\Libraries\Auth\Drivers;

use System\Facades\Redis as R;
use System\Facades\Http;

class RedisAttemptDriver implements AttemptDriverInterface
{
    protected $guard;

    protected $ip;

    public function __construct(string $guard)
    {
        $this->guard = $guard;
        $this->ip = Http::ip();
    }

    public function getAttemptsCountOrFail()
    {
        if (($result = R::get("AUTH_ATTEMPT_COUNT_".md5($this->ip.$this->guard)))) {
            return (object) array('count' => $result);
        }
        return false;
    }

    public function increment()
    {
        $count = $this->getAttemptsCountOrFail();

        R::setex(
            "AUTH_ATTEMPT_COUNT".md5($this->ip.$this->guard),
            60*60,
            $count ? $count->count+1 :1
        );
    }



    public function startLockTime($lockTime)
    {
        $expire = strtotime("+ {$lockTime} seconds");

        R::expire("AUTH_ATTEMPT_COUNT".md5($this->ip.$this->guard), $expire);

        R::setex("AUTH_ATTEMPT_EXPIRE".md5($this->ip.$this->guard), $expire, $expire);
    }


    public function deleteAttempt()
    {
        R::delete("AUTH_ATTEMPT_COUNT".md5($this->ip.$this->guard));
        R::delete("AUTH_ATTEMPT_EXPIRE".md5($this->ip.$this->guard));
    }



    public function expireTimeOrFail()
    {
        return R::get("AUTH_ATTEMPT_EXPIRE".md5($this->ip.$this->guard));
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
