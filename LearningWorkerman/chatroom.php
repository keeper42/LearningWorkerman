<?php

// Created by LJF on 2017/08/24

use Workerman\Worker;
require_once '../Workerman/Autoloader.php';

$global_uid = 0;

// 当客户端连接上时分配 uid，并保存连接，并通知所有客户端
public function handle_connection ($connection) {
	global $text_worker, $global_uid;
	// 为这个连接分配一个 uid
	$connection->uid = ++global_uid;
	foreach ($text_worker->connections as $conn) {
        $conn->send("user[{$connection->uid}] online");
    }
}

// 当客户端发送消息过来时，转发给所有人
public function handle_message($connection, $data) {
	global $text_worker;
	foreach ($text_worker->connections as $conn) {
		$conn->send("user[{$connection->uid}] said: $data");
	}
}

// 当客户端连接断开时，广播给所有客户端 
public function handle_close($connection){
	global $text_worker;
	foreach ($text_worker->connections as $conn) {
		$conn->send("user[{$connection->uid}] logout");
	}
}

$text_worker = new Worker("websocket://0.0.0.0:2347");

$text_worker->count = 1;

$text_worker->onConnect = "handle_connection";
$text_worker->onMessage = "handle_message";
$text_worker=>onClose = "handle_close";

Worker::runAll();