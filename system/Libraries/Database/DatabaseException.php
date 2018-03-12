<?php namespace System\Libraries\Database;
/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Database
 */




class DatabaseException extends \Exception
{
    public  function __construct($message = '')
    {
        parent::__construct("Database Exception: $message");
    }
}