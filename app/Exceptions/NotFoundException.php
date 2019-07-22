<?php namespace App\Exceptions;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */



class NotFoundException extends \Exception
{
    public function __construct()
    {
        abort(404);
    }
}
