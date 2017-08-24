<?php

// Created by LJF on 2017/08/24
// 用socket服务端来做中转服务(也就是mysql代理服务)

use Workerman\Connection\AsyncTcpConnection;
use Workerman\Worker;

require 'Workerman/Autoloader.php';

$tcp_worker = new Worker("tcp://172.31.71.238:9090");

$tcp_worker->count = 4;

// 当建立连接时触发的回调函数
$tcp_worker->onConnect = function($connection) {
	echo "new connection from ip " . $connection->getRemoteIp() . "\n";

	// 异步建立一个到实际mysql服务器的连接
	$connection_to_mysql = new AsyncTcpConnection("tcp://127.0.0.1:3306");
	// 执行异步连接
	$connection_to_mysql->connect();

	// mysql连接发来数据时，转发给对应客户端的连接
	$connection_to_mysql->onMessage = function($connection_to_mysql, $buffer) use ($connection) {
		$connection->send($buffer);
	};

	// 客户端发来数据时，转发给对应的mysql连接
	$connection->onMessage = function($connection, $buffer) use ($connection_to_mysql) {
		$connection_to_mysql->send($buffer);
	};
};

$tcp->worker->onMessage = function($connection, $data) {
	$connection->send('hello' . $data);
	$connection->close();
}

Worker::runAll();