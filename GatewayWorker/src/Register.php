<?php

// Created by LJf on 2017/08/23

namespace GatewayWorker;

use Workerman\Worker;
use Workerman\Lib\Timer;

/**
 * 注册中心，用于注册 Gateway 和 BusinessWorker
 */
class Register extends Worker 
{

	public $name = "Register";

	public $reloadable = false;

	public $secretKey = '';

	protected $_gatewayConnections = array();

	protected $_workerConnections = array();

	protected $_startTime = 0;

	public function run() {
		// 设置 onConnect 连接回调
		$this->onConnect = array($this, 'onConnect');

		// 设置 onMessage 回调
		$this->onMessage = array($this, 'onMessage');

		// 设置 onClose 回调
		$this->onClose = array($this, 'onClose');

		// 记录进程启动的时间
		$this->_startTime = time();

		// 强制使用text协议
		$this->protocol = '\Workerman\Protocols\Text';

		parent::run();
	}

	/**
	 * 设置定时器，将未及时发送验证的连接关闭
	 */
	public function onConnect($connection) {
		$connection->timeout_timerid = Timer::add(10, function() use ($connection) {
			Worker::log("Register auth timeout (".$connection->getRemoteIp()."). See http://wiki.workerman.net/Error4 for detail");
			$connection->close();
		}, null, false); 
	}

	/**
	 * 设置消息回调
	 */
	public function onMessage($connection, $buffer) {
		// 删除定时器
		Timer::del($connection->timeout_timerid);
		$data = @json_decode($buffer, true);
		if (empty($data['event'])) {
			$error = "Bad request for Register service. Request info(IP:".$connection->getRemoteIp().", Request Buffer:$buffer). See http://wiki.workerman.net/Error4 for detail";
			Worker::log($error);
			return $connection->close($error);
		}
		$event = $data['event'];
		$secret_key = isset($data['secret_key']) ? $data['secret_key'] : '';

		// 开始验证
		switch($event) {
			// gateway连接
			case 'gateway_connect':
			    if (empty($data['address'])) {
			        echo "address not found\n";
			        return $connection->close();
			    }
			    if ($secret_key !== $this->secretKey) {
			        Worker::log("Register: Key does not match ".var_export($secret_key, true)." !== ".var_export($this->secretKey, true));
			        return $connection->close();
			    }
			    $this->_gatewayConnections[$connection->id] = $data['address'];
			    $this->broadcastAddresses();
			    break;
			// worker 连接
			case 'worker_connect':
			    if ($secret_key !== $this->secretKey) {
			        Worker::log("Register: Key does not match ".var_export($secret_key, true)." !== ".var_export($this->secretKey, true));
			        return $connection->close();
			    }
			    $this->_workerConnections[$connection->id] = $connection;
			    $this->broadcastAddresses($connection);
			    break;
			case 'ping':
			    break;
			default:
			    Worker::log("Register unknown event:$event IP: ".$connection->getRemoteIp()." Buffer:$buffer. See http://wiki.workerman.net/Error4 for detail");
				$connection->close();
		}
	}

	/**
	 * 连接关闭时
	 */
	public function onClose($connection) {
		if (isset($this->_gatewayConnections[$connection->id])) {
			unset($this->_gatewayConnections[$connection->id]);
			$this->broadcastAddresses();
		}
		if (isset($this->_workerConnections[$connection->id])) {
			unset($this->_workerConnections[$connection->id]);
		}
	}

	/**
	 * 向 BusinessWorker 广播 gateway 内部通讯地址
	 */
	public function broadcastAddresses($connection = null) {
		$data = array(
			'event'     => 'broadcastAddresses',
			'addresses' => array_unique(array_values($this->_gatewayConnections)),
		);

		$buffer = json_encode($data);
		if ($connection) {
			$connection->send($buffer);
			return;
		}
		foreach($this->_workerConnections as $con) {
			$con->send($buffer);
		}
	}

}