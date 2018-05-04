<?php

namespace app\components\tj;


use app\components\CachedTrait;
use app\components\Config;
use app\components\Db;

class AI {

	use CachedTrait;

	public static function savePostedComment(Post $post, $comment_id, string $answer, int $reply_to = 0): void
	{
		Db::i()->insert('comments', ['postid' => $post->id, 'comment_id' => $comment_id, 'comment_text' => $answer, 'reply_to' => $reply_to, 'datetime' => time()]);
		Db::i()->update('posts', ['comments_count' => $post->commentsCount + 1], ['postid' => $post->id]);
	}

	/**
	 * @param $post
	 *
	 * @return mixed
	 * @throws \RuntimeException
	 */
	public static function randomComment($post)
	{
		$lastPostedRootComments = self::getLastRootComments(10);

		$conf = self::loadConfig();

		// Пытаемся стриггериться на текст поста
		if ($answer = self::triggerByText($post->id, $post->header . ' ' . $post->text)) {
			return $answer;
		}

		// Пытаемся стриггериться на автора поста
		if ($answer = self::triggerByText($post->id, $post->author->id)) {
			return $answer;
		}

		$comments = $conf['comments'];
		$commentsList = (array)$comments($post->header, $post->author, $post->text);

		shuffle($commentsList);

		// Проходимся по всем вариантам комментариев
		foreach ($commentsList as $comment) {
			// Если этот вариант мы не использовали в последних 10 своих комментариях
			if (!\in_array($comment, $lastPostedRootComments, true)) {
				return $comment;
			}
		}

		// Если вдруг все использовали, то берем случайный
		return reset($commentsList);
	}

	/**
	 * Возвращает список последних $limit моих корневых (не ответов) комментариев
	 *
	 * @param int $limit
	 *
	 * @return array
	 */
	public static function getLastRootComments($limit = 10): array
	{
		$comments = Db::i()->select('comments', '*', ['reply_to' => 0, 'ORDER' => ['datetime' => 'DESC'], 'LIMIT' => $limit]);

		$return = [];

		if (!empty($comments)) {
			foreach ($comments as $comment) {
				$return[$comment['id']] = $comment['comment_text'];
			}
		}

		return $return;
	}

	/**
	 * @return mixed
	 * @throws \RuntimeException
	 */
	public static function loadConfig()
	{
		return Config::load('tjai');
	}

	/**
	 * @param $postid
	 * @param $text
	 *
	 * @return bool|string
	 * @throws \RuntimeException
	 */
	public static function triggerByText($postid, $text)
	{
		$conf = self::loadConfig();
		$triggers = $conf['answers'];

		$sentComments = self::getMyCommentsTexts($postid);

		$text = mb_strtolower($text);

		foreach ((array)$triggers as $trigger) {
			$matches = [];
			$answers = $trigger['answers'];

			foreach ((array)$trigger['triggers'] as $trig) {

				if (preg_match('#' . $trig . '#ui', $text, $matches)) {

					if (\is_callable($answers)) {
						$answers = $answers($text, $matches);
					}

					shuffle($answers);

					foreach ((array)$answers as $answer) {
						if (!\in_array($answer, $sentComments, true)) {
							return $answer;
						}
					}
				}
			}
		}

		return false;
	}

	public static function getMyCommentsTexts($postid): array
	{
		return array_column(self::getPostedComments($postid), 'comment_text');
	}

	public static function getPostedComments($postid): array
	{
		return self::getStaticCached('postedComments-' . $postid, function () use ($postid)
		{
			$comments = Db::i()->select('comments', '*', ['postid' => $postid]);
			$return = [];

			if (!empty($comments)) {
				foreach ($comments as $comment) {
					$return[$comment['comment_id']] = $comment;
				}
			}

			return $return;
		});
	}

	/**
	 * @param int $postid
	 * @param Comment $comment
	 *
	 * @return bool|string
	 * @throws \RuntimeException
	 * @throws \Exception
	 */
	public static function trigger($postid, $comment)
	{
		if($mediaReply = self::triggerByMedia($postid, $comment)){
			return $mediaReply;
		}

		if ($personalReply = self::triggerByPerson($postid, $comment->author->id)) {
			return $personalReply;
		}

		return self::triggerByText($postid, $comment->text);
	}

	public static function triggerByMedia($postid, $comment){

		$conf = self::loadConfig();
		$mediaClosure = $conf['media'];

		$sentComments = self::getMyCommentsTexts($postid);

		if(!empty($comment->media)){
			foreach($comment->media as $media) {
				$mediaReplies = $mediaClosure($media);

				if(!empty($mediaReplies)){
					shuffle($mediaReplies);

					foreach($mediaReplies as $reply){
						if(!\in_array($reply, $sentComments, true)){
							return $reply;
						}
					}
				}
			}
		}

		return false;
	}

	/**
	 * @param int $postid
	 * @param int $user_id
	 *
	 * @return string|bool
	 * @throws \RuntimeException
	 * @throws \Exception
	 */
	public static function triggerByPerson($postid, $user_id)
	{
		if(random_int(0,100) > 75){
			return false;
		}

		$conf = self::loadConfig();
		$sentComments = self::getMyCommentsTexts($postid);

		$personalReplies = (array)$conf['personal']($user_id);
		shuffle($personalReplies);

		if ($personalReplies) {
			foreach ($personalReplies as $reply) {
				if (!\in_array($reply, $sentComments, true)) {
					return $reply;
				}
			}
		}

		return false;
	}

	public static function getMyReplyesTo($postid): array
	{
		$comments = array_column(self::getPostedComments($postid), 'reply_to');

		return array_unique(array_filter($comments));
	}

	/**
	 * Возвращает ответ на комментарий на основе текста исходного комментария
	 *
	 * @param $postid
	 * @param $comment
	 *
	 * @return mixed
	 * @throws \RuntimeException
	 */
	public static function getReply($postid, $comment)
	{
		$postedComments = self::getMyCommentsTexts($postid);

		$conf = self::loadConfig();

		$personalReplies = (array)$conf['personal']($comment);

		if ($personalReplies) {
			foreach ($personalReplies as $reply) {
				if (!\in_array($reply, $postedComments, true)) {
					return $reply;
				}
			}
		}

		$replies = (array)$conf['replies'];

		shuffle($replies);

		foreach ($replies as $reply) {
			// Если этот ответ еще не постили в этом после, то возвращаем его
			if (!\in_array($reply, $postedComments, true)) {
				return $reply;
			}
		}

		return false;
	}
}