<?php


/**
 * @param string $guard
 * @return \System\Facades\Auth
 */


function auth( $guard = 'user')
{
    return Auth::guard($guard);
}


/**
 * @param string $guard
 * @return Bool
 */

function isAuthentication($guard = 'user')
{
  return Auth::guard($guard)->check();
}
