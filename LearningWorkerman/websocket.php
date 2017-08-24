<?php

// Created by LJF on 2017/08/24

/**
 * Worker类的最常用3个回调方法
 * onConnect(进行连接)
 * onClose(关闭连接)
 * onMessage(前端发来消息)
 */
use Workerman\Worker;

require_once __DIR__ . '/Workerman/Autoloader.php';

// 创建一个worker
$ws_worker = new Worker("websocket://0.0.0.0:2346");

// 启动4个进程对外服务
$ws_worker->count = 4;

// 当收到客户端发来的数据后返回
$ws_worker->onMessage = function($connection, $data) {
	$connection->send("hello" . $data);
};

Worker::runAll();