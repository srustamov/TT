<?php namespace System\Libraries\Auth\Drivers;




use System\Facades\Session;


class SessionAttemptDriver implements AttemptDriverInterface
{



    public function getAttemptsCountOrFail($guard)
    {
        if($count = Session::get("AUTH_ATTEMPT_COUNT_{$guard}"))
        {
            return (object) array('count' => $count);
        }
        return false;
    }

    public function addAttempt($guard)
    {
        if($this->getAttemptsCountOrFail($guard))
        {
            Session::set("AUTH_ATTEMPT_COUNT_{$guard}",function($session) use ($guard){
                return $session->get("AUTH_ATTEMPT_COUNT_{$guard}")+1;
            });
        }
        else
        {
            Session::set("AUTH_ATTEMPT_COUNT_{$guard}",1);
        }

    }



    public function startLockTime($guard,$lock_time)
    {
        Session::set("AUTH_ATTEMPT_EXPIRE_{$guard}", strtotime("+ {$lock_time} seconds"));
    }


    public function deleteAttempt($guard)
    {
        Session::delete(array("AUTH_ATTEMPT_COUNT_{$guard}","AUTH_ATTEMPT_EXPIRE_{$guard}"));
    }



    public function expireTimeOrFail($guard)
    {
        return Session::get("AUTH_ATTEMPT_EXPIRE_{$guard}");
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
