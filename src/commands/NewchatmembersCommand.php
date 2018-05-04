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
 * New chat member command
 */
class NewchatmembersCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'newchatmembers';

    /**
     * @var string
     */
    protected $description = 'New Chat Members';

    /**
     * @var string
     */
    protected $version = '1.2.0';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();

        $chat_id = $message->getChat()->getId();
        $members = $message->getNewChatMembers();

		$text = 'Алерт, полку пидоров прибыло. ';

        if (!$message->botAddedInChat()) {
            $member_names = [];
            foreach ($members as $member) {
                $member_names[] = $member->tryMention();
            }
			$cnt = \count($member_names);
			if ($cnt === 1) {
				$texts = [
					reset($member_names) . ', а ты еще что за хуй такой?',
					'Пссс, ' . reset($member_names) . ', тебе уаз не нужен?',
				];
				$text .= $texts[random_int(0, \count($texts) - 1)];
			} else {
				$text .= implode(', ', $member_names) . ', пришлите сиськи в ЛС!';
			}
        }

        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];

        return Request::sendMessage($data);
    }
}
