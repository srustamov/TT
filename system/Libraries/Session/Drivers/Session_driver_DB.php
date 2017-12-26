<?php namespace System\Libraries\Session\Drivers;





use System\Libraries\Database\Database as HandlerDB;


class Session_driver_DB implements \SessionHandlerInterface
{


  private $table;

  private $db;


  function __construct($table)
  {
    $this->db     = new HandlerDB();
    $this->table  = $table;
  }

  public function open($save_path,$name):Bool
  {
    return true;
  }



  public function read($id):String
  {
    $result = $this->db->pdo()->query("SELECT data FROM {$this->table} WHERE session_id='{$id}'AND expires > ".time()."");
    if($result->rowCount() > 0)
    {
      return $result->fetch()->data;
    }
    return "";
  }



  public function write($id,$data):Bool
  {
    $time    = time() + ini_get('session.gc_maxlifetime');
    $result  = $this->db->pdo()->query("REPLACE INTO {$this->table} SET session_id ='{$id}',expires = {$time},data ='{$data}'");
    return $result ? true :false;

  }


  public function close():Bool
  {
    return $this->gc(ini_get('session.gc_maxlifetime'));
  }



  public function destroy($id):Bool
  {
    try
    {
      $this->db->pdo()->query("DELETE FROM {$this->table} WHERE session_id = '{$id}'");
    }
    catch (\PDOException $e){}

    return  true;
  }



  public function gc($maxlifetime):Bool
  {
    $this->db->pdo()->query("DELETE FROM {$this->table} WHERE expires < ".(time() + $maxlifetime));
    return true;
  }





}
