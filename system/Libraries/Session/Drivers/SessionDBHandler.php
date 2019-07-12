<?php namespace System\Libraries\Session\Drivers;

use SessionHandlerInterface;
use System\Facades\DB;

class SessionDBHandler implements SessionHandlerInterface
{
    private $table;


    public function __construct($table)
    {
        $this->table  = $table;
    }

    public function register()
    {
        session_set_save_handler(
          array($this, "open"),
          array($this, "close"),
          array($this, "read"),
          array($this, "write"),
          array($this, "destroy"),
          array($this, "gc")
      );
    }



    public function open($save_path, $name):Bool
    {
        return true;
    }



    public function read($id):String
    {
        $result = DB::pdo()->query("SELECT data FROM {$this->table} WHERE session_id='{$id}'AND expires > ".time()."");

        if ($result->rowCount() > 0) {
            return $result->fetch()->data;
        }
        return "";
    }



    public function write($id, $data):Bool
    {
        $time    = time() + (int) ini_get('session.gc_maxlifetime');

        $result  = DB::pdo()->query("REPLACE INTO {$this->table} SET session_id ='{$id}',expires = {$time},data ='{$data}'");

        return $result ? true :false;
    }


    public function close():Bool
    {
        return $this->gc((int) ini_get('session.gc_maxlifetime'));
    }



    public function destroy($id):Bool
    {
        try {
            DB::pdo()->query("DELETE FROM {$this->table} WHERE session_id = '{$id}'");
        } catch (\PDOException $e) {
        }

        return  true;
    }



    public function gc($maxlifetime):Bool
    {
        DB::pdo()->query("DELETE FROM {$this->table} WHERE expires < ".(time() + $maxlifetime));

        return true;
    }
}
