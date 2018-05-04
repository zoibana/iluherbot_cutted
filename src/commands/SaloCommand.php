<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/salo" command
 *
 */
class SaloCommand extends UserCommand {
	/**
	 * @var string
	 */
	protected $name = 'salo';

	/**
	 * @var string
	 */
	protected $description = 'Сало';

	/**
	 * @var string
	 */
	protected $usage = '/salo';

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

		$texts = [
			'https://edaplus.info/food_pictures/fat.jpg',
			'https://takprosto.cc/wp-content/uploads/p/polza-sala/thumb.jpg',
			'http://strana-sovetov.com/images/stories/2015/06/zagotovka-sala-na-zimuj-8.jpg',
			'http://gastrot.ru/wp-content/uploads/2017/11/salo.jpg',
			'http://memesmix.net/media/created/tsq9a1.jpg',
		];

		$data = [
			'chat_id' => $chat_id,
			'photo' => $texts[random_int(0, \count($texts)-1)],
		];

		return Request::sendPhoto($data);
	}

}
