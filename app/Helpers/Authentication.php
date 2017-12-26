<?php


/**
 * @param null $guard
 * @return \System\Libraries\Auth\Authentication|static
 * @throws Exception
 */

use System\Libraries\Auth\Authentication;


function auth( $guard = null)
{
    if(class_exists('System\Libraries\Auth\Authentication')) {
        return is_null($guard)
            ? (new Authentication())
            : (new Authentication())->guard($guard);
    }
    throw new Exception('System\Libraries\Auth\Authentication class Not Found');

}


function isAuthentication($guard = null)
{
  return auth($guard)->check();
}
