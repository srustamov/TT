<?php namespace System\Exceptions;



class DatabaseException extends \Exception
{
    public function __construct($message ='',$query = '')
    {
      parent::__construct(" $message <br />"."[QUERY: $query]");
    }
}
