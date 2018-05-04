<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/mylove" command
 *
 */
class MyloveCommand extends UserCommand {
	/**
	 * @var string
	 */
	protected $name = 'mylove';

	/**
	 * @var string
	 */
	protected $description = 'Моя любовь';

	/**
	 * @var string
	 */
	protected $usage = '/mylove';

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
		$username = $message->getFrom()->getUsername();

		$texts = [
			'Тебя никто не любит, '.$username,
			'Ты и правда надеешься, что тебя хоть кто-то любит, '.$username.'?!',
			'ты себя в зеркало-то видело, '.$username.'?',
			'АХАХАХАХАХА МОЯ ЛЮБОВЬ сук! '.$username.' ХОЧЕТ БОЛЬШОЙ И ЧИСТОЙ ЛЮБВИ? Сосни!',
			'С тобой не спит даже кот, '.$username,
			'Ну ты хотя бы сам себе не пизди, '.$username.'. Тебя уже никто не полюбит',
			'Тебя любит мамка, '.$username,
			'Ты веришь предсказаниям бота, '.$username.'? Может еще гороскоп почитаешь? Ну попробуй => /horoscope',
			$username.' - forever alone',
			'ты блять носки меняешь раз в месяц, какая нахуй любовь, '.$username.'?',
		];

		$data = [
			'chat_id' => $chat_id,
			'text' => $texts[random_int(0, \count($texts)-1)],
		];

		return Request::sendMessage($data);
	}

}
