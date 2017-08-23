<?php

// Created by LJF on 2017/08/22

use \Workerman\Worker;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;

require_once '/../../vendor/autoload.php';

$worker = new BusinessWorker();

$worker->name = "ChatBusinessWorker";

$worker->count= 4;

$worker->registerAddress = "127.0.0.1:1236";

if (!defined("GLOBAL_START")) {
	Worker::runAll();
}