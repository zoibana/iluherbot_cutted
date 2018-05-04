<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/help" command
 *
 * Command that lists all available commands and displays them in User and Admin sections.
 */
class PidorCommand extends UserCommand {
	/**
	 * @var string
	 */
	protected $name = 'pidor';

	/**
	 * @var string
	 */
	protected $description = 'Говорит правду';

	/**
	 * @var string
	 */
	protected $usage = '/pidor';

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
		$name = $message->getFrom()->getFirstName() ?? $message->getFrom()->getUsername();
		$username = '*' . $name . '*';

		$data = [
			'chat_id' => $chat_id,
			'parse_mode' => 'Markdown',
		];

		$blames = [
			$username . ', Твоя мамка жирная',
			$username . ' гей!',
			$username . ' пидор',
			'Цыплухин - пидор',
			'Илюхер - лох',
			'Вы все геи. Особенно ' . $username . '. Одна Надя солнышко',
			$username . ', ты хуй и говно',
			'Кстати ' . $username . ' нахуй иди',
			$username . ', я мамку твою ебал, например :*',
			$username . ' - хуй. Я - пидор. Он - говноед. Она - шлюха. Ну и что? Ну и ничего.',
			'Всё вы врёти, здесь все умные, правильные и рассудительные сидят, один ' . $username . ' долбоёб и не лечится',
		];

		$data['text'] = $blames[random_int(0, \count($blames) - 1)];

		return Request::sendMessage($data);
	}

}
