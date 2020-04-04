<?php

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link 	https://github.com/srustamov/TT
 */


$this->namespace('App\\Controllers')
  ->middleware(['start_session', 'csrf'])
  ->group(function () {
    require __DIR__ . '/includes/web.php';
  });

// $this->namespace('App\\Controllers\\Api')
//   ->prefix('/api')
//   ->middleware(['cors','api', 'auth:api'])
//   ->group(function(){
//      require __DIR__.'/includes/api.php';
//   });
