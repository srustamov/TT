<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

use Socket\Message;

require __DIR__.'/Message.php';

$port   = 9000;

$server = IoServer::factory( new HttpServer( new WsServer( new Message())),$port);


$server->run();

