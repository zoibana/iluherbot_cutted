<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/mylove" command
 *
 */
class HoroscopeCommand extends UserCommand {
	/**
	 * @var string
	 */
	protected $name = 'horoscope';

	/**
	 * @var string
	 */
	protected $description = 'Гороскоп';

	/**
	 * @var string
	 */
	protected $usage = '/horoscope';

	/**
	 * @var string
	 */
	protected $version = '1.3.0';

	/**
	 * @inheritdoc
	 */
	public function execute() {
		$message = $this->getMessage();
		$chat_id = $message->getChat()->getId();

		$data = [
			'chat_id' => $chat_id,
			'text' => 'Серьезно? Гороскоп?? Какой нахуй гороскоп? Иди блять посри, толку больше будет',
		];

		return Request::sendMessage($data);
	}

}
