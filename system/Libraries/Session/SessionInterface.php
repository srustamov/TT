<?php namespace System\Libraries\Session;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage    Libraries
 * @category    SessionInterface
 */





interface SessionInterface
{




    public function __construct();


    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value);


    /**
     * @param Array $data
     */
    public function setArray(Array $data);

    /**
     * @param $key
     * @return Bool
     */

    public function get($key);


    /**
     * @return Array
     */
    public function all():array;

    /**
     * @param $key
     * @return Bool
     */
    public function has($key):Bool;


    /**
     * @param $key
     */
    public function delete($key);




    public function path($path = null);




    public function domain($domain = null);




    public function destroy();
}
