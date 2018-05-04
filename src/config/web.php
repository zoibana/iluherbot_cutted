<?php

return [
	'debug_mode' => true,
	'error_mode' => [
		// Уровни ошибок, при которых выполнение скрипта не будет прервано, а ошибки будут выведены (на экран, в консоль и т.д.)
		'tolerant' => E_WARNING | E_USER_WARNING | E_DEPRECATED | E_USER_DEPRECATED | E_ERROR | E_USER_ERROR,
		// Все остальные уровни ошибок (за исключением указанных в блоке tolerant), которые будут считаться критическими и которые будут прерывать работу скрипта
		'critical' => E_ALL ^ E_NOTICE,
	],
	'components' => [
		'logger' => [
			'class' => \app\components\log\Logger::class,
			'processors' => [
				\app\components\log\processors\EnviromentProcessor::class,
			],
			'targets' => [
				'fileLog' => [
					'class' => \app\components\log\targets\FileLog::class,
					'file' => \app\Application::getAlias('@runtime').'log/application.log',
					'level' => \Monolog\Logger::DEBUG, // minimum log level for logging
					'maxFiles' => 5, // max files before rotations
				],
				'mailLog' => [
					'class' => \app\components\log\targets\MailLog::class,
					'level' => \Monolog\Logger::WARNING, // minimum log level for logging
					'mailTo' => 'your@mail.address',
					'formatter' => \Monolog\Formatter\HtmlFormatter::class,
				],
			],
		],
		'mailer' => [
			'class' => \app\components\Mailer::class,
			'from' => ['from@mail.address', 'From Name'],
		],
		'response' => [
			'class' => \app\components\http\Response::class,
		],
		'urls' => [
			'class' => \app\components\UrlManager::class,
			'urls' => [
				'/' => 'site/index',
				'/tj/index/{page:\d+}/' => 'tj/index',
				'/tj/{action:\w+}/' => 'tj/{action}',
				'/bot/{action:\w+}/' => 'bot/{action}',
			],
		],
		'view' => [
			'class' => \app\components\View::class,
			'viewsPath' => \app\Application::getAlias('@app').'views',
			'templateExtension' => 'php',
			'errorTemplate' => '/error/error',
		],
	],
];