<?php

// Created by LJF on 2017/08/23

namespace GatewayWorker\Lib;

use Config\Db as DbConfig;
use Exception;

class Db
{

	protected static $instance = array();

	public static function instance($config_name) {
		if (!isset(DbConfig::$config_name)) {
			echo "\\Config\\Db::$config_name not set\n";
			throw new Exception("\\Config\\Db::$config_name not set\n");
		}

		if (empty(self::$instance[$config_name])) {
			$config = DbConfig::$config_name;
			self::$instance[$config_name] = new DbConnection($config['host'], $config['port'], 
				$config['user'], $config['password'], $config['dbname']);
		}
		return self::$instance[$config_name];
	}

	public static function close($config_name) {
		if (isset(self::$instance[$config_name])) {
			self::$instance[$config_name]->closeConnection();
			self::$instance[$config_name] = null;
		}
	}

	public static function closeAll() {
		foreach (self::$instance as $connection) {
			$connection->closeConnection();
		}
		self::$instance = array();
	}

}