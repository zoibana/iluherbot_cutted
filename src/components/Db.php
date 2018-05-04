<?php

namespace app\components;


use Medoo\Medoo;

class Db
{
	private static $inst;

	public static function i(): Medoo
	{
		if (self::$inst === null) {

			$config = Config::load('db');

			self::$inst = new Medoo([
				'charset' => 'utf8',
				'database_type' => 'mysql',
				'database_name' => $config['dbname'],
				'server' => $config['dbhost'],
				'username' => $config['login'],
				'password' => $config['password'],
			]);

		}

		return self::$inst;
	}

	protected function __construct()
	{
	}

	protected function __clone()
	{
		// TODO: Implement __clone() method.
	}
}