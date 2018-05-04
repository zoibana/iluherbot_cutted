<?php

namespace app\components\telegrambot;

use app\components\Config;
use Longman\TelegramBot\Entities\Message;

class AI {

	/** @var array */
	protected $answers;

	/**
	 * AI constructor.
	 *
	 * @throws \RuntimeException
	 */
	public function __construct() {
		$this->answers = Config::load('answers');
	}

	/**
	 * 1. string $regex
	 * 2. array $answer
	 *
	 * @param Message $message
	 * @param $text
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function getAnswerData(Message $message, $text): ?array {

		$data = [
			'chat_id' => $message->getChat()->getId(),

		];

		if (!empty($this->answers['voice']) && $message->getVoice() && random_int(0,100) > 75) {
			$data['text'] = $this->answers['voice'][random_int(0, \count($this->answers['voice'])-1)];
			$data['reply_to_message_id'] = $message->getMessageId();
			return $data;
		}

		if (!empty($this->answers['answers'])) {

			foreach ((array)$this->answers['answers'] as $answer) {

				if (!empty($answer['triggers'])) {

					foreach ((array)$answer['triggers'] as $trigger) {

						$trigger = trim($trigger);

						if (strpos($trigger, '=') === 0) {
							$trigger = str_replace('=', '', $trigger);
							$matched = $text === $trigger;
						} else {
							$matched = strpos($text, $trigger) !== false;
						}

						if (!empty($answer['in_reply_to_me'])) {
							$config = Config::load('telegrambot');
							$matched = $matched && $message->getFrom()->getBotUsername() === $config['bot_username'];
						}

						if (!empty($answer['trigger_user_ids'])) {
							$matched = $matched && \in_array($message->getFrom()->getId(), $answer['trigger_user_ids'], true);
						}

						if ($matched) {
							$answers = $answer['answers'];
							$ans = $answers[random_int(0, \count($answers))];

							if (strpos($ans, 'photo:') === 0) {
								$data['photo'] = str_replace('photo:', '', $ans);
							} elseif (strpos($ans, 'sticker:') === 0) {
								$data['sticker'] = str_replace('sticker:', '', $ans);
							} else {
								$data['text'] = $ans;
							}

							if (!empty($answer['reply'])) {
								$data['reply_to_message_id'] = $message->getMessageId();
							}

							return $data;
						}
					}
				}
			}
		}

		return [];
	}
}