<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use app\components\telegrambot\AI;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

/**
 * Generic message command
 *
 * Gets executed when any type of message is sent.
 */
class GenericmessageCommand extends SystemCommand {
	/**
	 * @var string
	 */
	protected $name = 'genericmessage';

	/**
	 * @var string
	 */
	protected $description = 'Handle generic message';

	/**
	 * @var string
	 */
	protected $version = '1.1.0';

	/**
	 * Command execute method
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \RuntimeException
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$chat = $message->getChat();
		$chatType = $chat->type;
		$isPrivate = $chatType === 'private';

		if(!$isPrivate && random_int(0,100) < 30){
			return Request::emptyResponse();
		}

		$text = mb_strtolower(trim($message->getText()));

		$answers = new AI();
		$data = $answers->getAnswerData($message, $text);
		$sticker = $message->getSticker();
		$stikerId = $sticker? $sticker->getFileId(): 0;

		$logmessage = date('Y-m-d H:i:s') . ':  ' .  $message->getFrom()->getUsername() . ' : '. $text.", sticker_id: '.$stikerId.'\r\n";
		//file_put_contents(__DIR__. '/../bot.log', $logmessage, FILE_APPEND);

		if (!empty($data['photo'])) {
			return Request::sendPhoto($data);
		}

		if(!empty($data['sticker'])){
			return Request::sendSticker($data);
		}

		if (!empty($data['text'])) {
			return Request::sendMessage($data);
		}

		return Request::emptyResponse();
	}
}
