<?php

namespace app\components\log\processors;


class EnviromentProcessor {

	public function __invoke($record)
	{
		$record['extra']['env'] = self::getEnvInfo();

		return $record;
	}

	public static function getEnvInfo(): array
	{
		$data =  [
			'referer' => $_SERVER['HTTP_REFERER'] ?? '',
			'path' => 'http://' . $_SERVER['SERVER_NAME'] . urldecode($_SERVER['REQUEST_URI']),
			'user-agent' => $_SERVER['HTTP_USER_AGENT']?? '--',
			'ip' => $_SERVER['REMOTE_ADDR']?? '--',
			'trace' => tracer(100),
			'$_GET'               => $_GET ?? [],
			'$_POST'              => $_POST ?? [],
			'$_FILES'             => $_FILES ?? [],
			'$_COOKIE'            => $_COOKIE ?? [],
			'$_SESSION'           => $_SESSION ?? [],
			'$_SERVER'            => $_SERVER ?? [], //			'$_ENV' => $_ENV,
			'RAW REQUEST HEADERS' => \function_exists('getallheaders') ? getallheaders() : [],
		];

		return $data;
	}
}