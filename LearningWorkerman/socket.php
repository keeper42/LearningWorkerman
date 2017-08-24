<?php

// Created by LJF on 2017/08/24

use Workerman\Worker;

require_once 'Workerman/Autoloader.php';

$tcp_worker = new Worker("tcp://172.31.71.238:9090");

$tcp_worker->count = 4;

// 当连接建立时触发的回调函数
$tcp_worker->onConnect = function($connection) {
	echo "new connection from ip " . $connection->getRemoteIp() . "\n";
}

// 当客户端发来数据时
$tcp_worker->onMessage = function($connection, $data) {
	$connection->send("hello" . $data);
	$connection->close();
}

Worker::runAll();
