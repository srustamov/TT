<?php namespace System\Libraries\Session\Drivers;



use SessionHandlerInterface;
use System\Facades\Redis as DRedis;


class SessionRedisStore implements SessionHandlerInterface
{


  public function open($save_path, $session_name):Bool
  {
    return true;
  }


  public function close():Bool
  {
    return $this->gc(ini_get('session.gc_maxlifetime'));
  }



  public function read($id)
  {
    return ''.DRedis::get('session_'.$id);
  }



  public function write($id,$session_data):Bool
  {
    DRedis::setex('session_'.$id,ini_get('session.gc_maxlifetime'),$session_data);
    
    return true;
  }




  public function destroy($id):Bool
  {
    DRedis::delete('session_'.$id);

    return true;
  }



  public function gc($maxlifetime):Bool
  {
    return true;
  }



}
