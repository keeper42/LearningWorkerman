<?php

// Created by LJf on 2017/08/22

use \Workerman\Worker;
use \Workerman\WebServer;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;

require_once '/../../vendor/autoload.php';

$web = new WebServer("http://0.0.0.0:55151");

$web->count = 2;

$web->addRoot("www.your_domain.com", __DIR__."/Web");

if (!defined("GLOBAL_START")) {
	Worker::runAll();
}