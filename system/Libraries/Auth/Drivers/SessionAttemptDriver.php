<?php namespace System\Libraries\Auth\Drivers;

use System\Facades\Session;

class SessionAttemptDriver implements AttemptDriverInterface
{
    public function getAttemptsCountOrFail()
    {
        if ($count = Session::get("AUTH_ATTEMPT_COUNT")) {
            return (object) array('count' => $count);
        }
        return false;
    }

    public function increment()
    {
        if ($this->getAttemptsCountOrFail()) {
            Session::set("AUTH_ATTEMPT_COUNT", function ($session) {
                return $session->get("AUTH_ATTEMPT_COUNT")+1;
            });
        } else {
            Session::set("AUTH_ATTEMPT_COUNT", 1);
        }
    }



    public function startLockTime($lockTime)
    {
        Session::set("AUTH_ATTEMPT_EXPIRE", strtotime("+ {$lockTime} seconds"));
    }


    public function deleteAttempt()
    {
        Session::delete(array("AUTH_ATTEMPT_COUNT","AUTH_ATTEMPT_EXPIRE"));
    }



    public function expireTimeOrFail()
    {
        return Session::get("AUTH_ATTEMPT_EXPIRE");
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
