<?php namespace System\Libraries\Auth\Drivers;


use System\Libraries\Auth\Drivers\Attempt_Driver_Interface;
use System\Libraries\Database\Database;



class Database_Attempt_Driver implements Attempt_Driver_Interface
{


  private $db;


  function __construct()
  {
    $this->db = new Database();
  }


  public function getAttemptsCountOrFail($guard)
  {
      return $this->db->table('attempts')->where('ip',$this->ip())->where('guard',$guard)->first();
  }

  public function addAttempt($guard)
  {
    if($this->getAttemptsCountOrFail($guard))
    {
      $this->db->pdo()->query("UPDATE attempts SET count = count+1 WHERE ip ='{$this->ip()}' AND guard='{$guard}'");
    }
    else
    {
      $this->db->pdo()->query("INSERT INTO attempts SET ip = '{$this->ip()}',count=1,guard='{$guard}'");
    }
  }


  public function startLockTime($guard,$lock_time)
  {
    $time = strtotime("+ {$lock_time} seconds");

    $this->db->pdo()->query("UPDATE attempts SET expiredate = '{$time}' WHERE ip ='{$this->ip()}' AND guard='{$guard}'");
  }


  public function deleteAttempt($guard)
  {
    $this->db->pdo()->query("DELETE FROM attempts WHERE ip ='{$this->ip()}' AND guard='{$guard}'");
  }



  public function expireDateOrFail($guard)
  {
    $result = $this->db->pdo()->query("SELECT expiredate FROM attempts WHERE ip='{$this->ip()}' AND guard='{$guard}'");
    if ($result->rowCount() > 0)
    {
      return $result->fetch()->expiredate;
    }

    return false;
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


  private function ip (): String
  {
    return \System\Facades\Http::ip();
  }



}
