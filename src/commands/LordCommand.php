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
class LordCommand extends UserCommand {
	/**
	 * @var string
	 */
	protected $name = 'lord';

	/**
	 * @var string
	 */
	protected $description = 'Говорит правду';

	/**
	 * @var string
	 */
	protected $usage = '/lord';

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
			'parse_mode' => 'Markdown',
			'text' => 'Наркоманы ебучие шоб вы сдохли',
		];

		return Request::sendMessage($data);
	}

}
