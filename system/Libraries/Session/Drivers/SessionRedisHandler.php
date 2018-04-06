<?php namespace System\Libraries\Session\Drivers;



use SessionHandlerInterface;
use System\Facades\Redis as DRedis;


class SessionRedisHandler implements SessionHandlerInterface
{


    public function open($save_path, $session_name):Bool
    {
        return true;
    }


    public function close():Bool
    {
        DRedis::close();

        return true;
    }



    public function read($id)
    {
        return ''.DRedis::get('session_'.$id);
    }



    public function write($id,$session_data):Bool
    {
        DRedis::setex('session_'.$id,(int) ini_get('session.gc_maxlifetime'),$session_data);

        return true;
    }




    public function destroy($id):Bool
    {
        DRedis::del('session_'.$id);

        return true;
    }


    public function regenerate($id)
    {
        /*
        if(($data = DRedis::get('session_'.$id))) {

            $this->destroy($id);

            $newId = session_create_id();

            $this->write($newId,$data);

        }

        return $newId ?? $id;

        */
    }



    public function gc($maxlifetime):Bool
    {
        return true;
    }



}
