<?php

namespace App\Exceptions;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */


use Exception;

class HttpNotFoundException extends Exception
{
    /**
     * HttpNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct();

        abort(404);
    }
}
