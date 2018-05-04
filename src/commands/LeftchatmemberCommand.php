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
 * Left chat member command
 *
 * Gets executed when a member leaves the chat.
 */
class LeftchatmemberCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'leftchatmember';

    /**
     * @var string
     */
    protected $description = 'Left Chat Member';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
	    $message = $this->getMessage();
	    $chat_id     = $message->getChat()->getId();
	    $text = 'Одним пидором меньше';

	    $data = [
		    'chat_id' => $chat_id,
		    'text'    => $text,
	    ];

	    return Request::sendMessage($data);
    }
}
