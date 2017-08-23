<?php

// Created by LJF on 2017/08/23

namespace GatewayWorker\Lib;

use Exception;

/**
 * 上下文类，包含当前用户uid，内部通信 local_ip local_port socket_id, 以及客户端 client_ip client_port
 */
class Context
{

	public static $local_ip;
	public static $local_port;
	public static $client_ip;
	public static $client_port;
	public static $client_id;
	public static $connection_id;
	public static $old_session;

	// 编码 session
	public static function sessionEncode($session_data = "") {
		if ($session_data !== "") {
			return serialize($session_data);
		}
		return "";
	}

	// 解码 session
	public static function sessionDecode($session_buffer) {
		return unserialize($session_buffer);
	}

	// 清除上下文
	public static function clear() {
		self::$local_ip = self::$local_port = self::$client_ip  = self::$client_port = 
		self::$client_id = self::$connection_id = self::$old_session = null;
	}

	// 通讯地址到 client_id 的转换
	public static function addressToClientId($local_ip, $local_port, $connection_id) {
		return bin2hex(pack('NnN', $local_ip, $local_port, $connection_id));
	}

	// client_id 到通讯地址的转换
	public static function clientIdToAddress($client_id) {
		if (strlen($client_id) != 20) {
			echo new Exception("client_id $client_id is invalid");
			return false;
		}
		return unpack('Nlocal_ip/nlocal_port/Nconnection_id', pack('H*', $client_id));
	}

}