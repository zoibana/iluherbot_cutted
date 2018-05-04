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

use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

/**
 * User "/help" command
 *
 * Command that lists all available commands and displays them in User and Admin sections.
 */
class QuoteCommand extends UserCommand
{
	/**
	 * @var string
	 */
	protected $name = 'quote';

	/**
	 * @var string
	 */
	protected $description = 'Лучшие цитаты';

	/**
	 * @var string
	 */
	protected $usage = '/quote';

	/**
	 * @var string
	 */
	protected $version = '1.3.0';

	/**
	 * @inheritdoc
	 */
	public function execute()
	{
		$message     = $this->getMessage();
		$chat_id     = $message->getChat()->getId();
		$username = $message->getChat()->getUsername();

		$data = [
			'chat_id'    => $chat_id,
		];

		$texts = [
			'Ты - хуй. Я - пидор. Он - говноед. Она - шлюха. Ну и что? Ну и ничего.',
			'В окружающих я регулярно вижу желание выебываться',
			'Забейте, я сосу хуи',
			'мамку твою ебал, например :*',
			'А у тебя сиськи маленькие, и чо?',
			'Пидор лохматый',
			'Кококо, много ремиксов, каверов, уникальных записей, кококо.',
			'ты хуй и говно',
			'Ох ты ж боже ж мой, какие чувствительные все, пиздец.',
			'Спорт для пидоров',
			'Знаток блядь, наркоман ебучий, Яхве всё видит.',
			'[Комментарий удалён]',
			'Зачем это здесь?',
			'Вы тут все тупорылые дебилы и не лечитесь',
			'Всё вы врёти, здесь все умные, правильные и рассудительные сидят, один илюхер долбоёб и не лечится',
			'Редакция, идите нахуй, пожалуйста',
			'Кто не пидор? Это я-то не пидор?',
			'Я ебу алибабу',
			'Вы там охуели штоле совсем',
			'Обожаю такую хуйню и смотреть за осбсосками, которые как стрелка осциллографа© мечутся из стороны в сторону, потому что им очень важно иметь и высказывать свое ебучее мнение, а также сообщать, как они шокированы, а их доверие подорвано. А потом в другую сторону. А потом снова в ту. И еще. И еще раз. И снова.',
			'Ты анимешник и дрочишь на плоских нарисованных лолей, так что не пизди мне тут',
		];

		$data['text'] = $texts[random_int(0, \count($texts)-1)];

		return Request::sendMessage($data);
	}

}
