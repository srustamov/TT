<?php namespace System\Libraries\Auth\Drivers;

use System\Facades\DB;
use System\Facades\Http;

class DatabaseAttemptDriver implements AttemptDriverInterface
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
        return DB::table('attempts')->where([
            'ip' => $this->ip,
            'guard' => $this->guard
        ])->first();
    }

    public function increment()
    {
        if ($this->getAttemptsCountOrFail()) {
            DB::pdo()->query("UPDATE attempts SET count = count+1 WHERE ip ='{$this->ip}' AND guard='{$this->guard}'");
        } else {
            DB::pdo()->query("INSERT INTO attempts SET ip = '{$this->ip}',guard='{$this->guard}',count=1");
        }
    }


    public function startLockTime($lockTime)
    {
        $time = strtotime("+ {$lockTime} seconds");

        DB::pdo()->query("UPDATE attempts SET expiredate = '{$time}' WHERE ip ='{$this->ip}' AND guard='{$this->guard}'");
    }


    public function deleteAttempt()
    {
        DB::pdo()->query("DELETE FROM attempts WHERE ip ='{$this->ip}' AND guard='{$this->guard}'");
    }



    public function expireTimeOrFail()
    {
        $result = DB::pdo()->query("SELECT expiredate FROM attempts WHERE ip='{$this->ip}' AND guard='{$this->guard}'");

        if ($result->rowCount() > 0) {
            return $result->fetch()->expiredate;
        }

        return false;
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
