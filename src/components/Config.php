<?php
namespace app\components;


class Config {

	protected static $configDir = __DIR__ . '/../config/';
	protected static $configExt = '.php';

	/**
	 * @param $configName
	 *
	 * @return mixed
	 * @throws \RuntimeException
	 */
	public static function load($configName) {
		$filePath = self::$configDir . $configName . self::$configExt;

		if(is_file($filePath)){
			return require $filePath;
		}

		throw new \RuntimeException("Config {$configName} is not found");
	}
}