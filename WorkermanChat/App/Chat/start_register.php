<?php

// Created by LJF on 2017/08/22

use \Workerman\Worker;
use \GatewayWorker\Register;

require_once __DIR__ . '/../../vendor/autoload.php';

// register 服务必须是text协议
$register = new Register("text://0.0.0.0:1236");

if (!defined("GLOBAL_START")) {
	Worker::runAll();
}