<?php

namespace app\controllers;

use app\components\Config;
use Longman\TelegramBot\Telegram;

class BotController extends Controller {

	/** @var Telegram */
	protected $tg;

	/**
	 * @throws \RuntimeException
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public function init(): void
	{
		parent::init();

		$config = Config::load('telegrambot');

		// Create Telegram API object
		$this->tg = new Telegram($config['api_key'], $config['bot_username']);

		// Add commands paths containing your custom commands
		$this->tg->addCommandsPaths($config['commands']);

		// Enable admin users
		$this->tg->enableAdmins($config['adminUsers']);

		// Enable MySQL
		//$telegram->enableMySql($mysql_credentials);

		// Logging (Error, Debug and Raw Updates)
		//Longman\TelegramBot\TelegramLog::initErrorLog(__DIR__ . "/{$bot_username}_error.log");
		//Longman\TelegramBot\TelegramLog::initDebugLog(__DIR__ . "/{$bot_username}_debug.log");
		//Longman\TelegramBot\TelegramLog::initUpdateLog(__DIR__ . "/{$bot_username}_update.log");

		// If you are using a custom Monolog instance for logging, use this instead of the above
		//Longman\TelegramBot\TelegramLog::initialize($your_external_monolog_instance);

		// Set custom Upload and Download paths
		//$telegram->setDownloadPath(__DIR__ . '/Download');
		//$telegram->setUploadPath(__DIR__ . '/Upload');

		// Here you can set some command specific parameters
		// e.g. Google geocode/timezone api key for /date command
		//$telegram->setCommandConfig('date', ['google_api_key' => 'your_google_api_key_here']);

		// Botan.io integration
		//$telegram->enableBotan('your_botan_token');
	}

	/**
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public function actionHook(): string
	{
		// Requests Limiter (tries to prevent reaching Telegram API limits)
		$this->tg->enableLimiter();

		// Включение логов
		//TelegramLog::initUpdateLog( 'update.log');

		$this->tg->handle();

		return '';
	}

	public function actionCron() {

		$commands = [
			'/whoami',
			"/echo I'm a bot!",
		];

		// Run user selected commands
		$this->tg->runCommands($commands);
	}

	public function actionSet(): string {
		$config = Config::load('telegrambot');
		$result = $this->tg->setWebhook($config['hook_url']);

		if ($result->isOk()) {
			return $result->getDescription();
		}

		return 'failed';
	}

	/**
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public function actionUnset() {
		// Delete webhook
		$result = $this->tg->deleteWebhook();

		if ($result->isOk()) {
			return $result->getDescription();
		}

		return 'failed';
	}

}