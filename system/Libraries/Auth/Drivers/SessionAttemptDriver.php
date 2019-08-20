<?php namespace System\Libraries\Auth\Drivers;

use System\Facades\Session;

class SessionAttemptDriver implements AttemptDriverInterface
{
    protected $guard;

    public function __construct(string $guard)
    {
        $this->guard = $guard;
    }

    public function getAttemptsCountOrFail()
    {
        if ($count = Session::get('AUTH_ATTEMPT_COUNT_'.$this->guard)) {
            return (object) array('count' => $count);
        }
        return false;
    }

    public function increment()
    {
        if ($this->getAttemptsCountOrFail()) {
            Session::set('AUTH_ATTEMPT_COUNT_'.$this->guard, function ($session) {
                return $session->get('AUTH_ATTEMPT_COUNT_'.$this->guard)+1;
            });
        } else {
            Session::set('AUTH_ATTEMPT_COUNT_'.$this->guard, 1);
        }
    }



    public function startLockTime($lockTime)
    {
        Session::set('AUTH_ATTEMPT_EXPIRE_'.$this->guard, strtotime('+ {$lockTime} seconds'));
    }


    public function deleteAttempt()
    {
        Session::delete(array('AUTH_ATTEMPT_COUNT_'.$this->guard,'AUTH_ATTEMPT_EXPIRE_'.$this->guard));
    }



    public function expireTimeOrFail()
    {
        return Session::get('AUTH_ATTEMPT_EXPIRE_'.$this->guard);
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
