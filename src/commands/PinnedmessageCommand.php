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

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

/**
 * Pinned message command
 *
 * Gets executed when a message gets pinned.
 */
class PinnedmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'pinnedmessage';

    /**
     * @var string
     */
    protected $description = 'Message was pinned';

    /**
     * @var string
     */
    protected $version = '1.0.0';

	/**
	 * Command execute method
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Exception
	 */
    public function execute()
    {
	    $message     = $this->getMessage();
	    $chat_id     = $message->getChat()->getId();

	    $data = [
		    'chat_id'    => $chat_id,
		    'text' => 'Анус себе запинь, пес',
	    ];

	    return Request::sendMessage($data);
    }
}
