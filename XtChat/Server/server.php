<?php

use Workerman\Worker;
require_once 'work/Autoloader.php';
require_once 'method.php';
require_once 'Server.class.php';
$ws = new Worker("websocket://0.0.0.0:10266");
$server = new Server($ws);

$ws->onConnect = function($connection){
	global $server;
	$server->onConnect($connection);
};
$ws->onClose = function($connection){
	global $server;
	$server->onClose($connection);
};
$ws->onMessage = function($connection, $data) {
	global $server;
	$server->onMessage($connection,json_decode($data,true));
};

// 运行worker
Worker::runAll();

?>