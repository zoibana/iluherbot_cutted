<?php

namespace app\controllers;

ini_set('memory_limit', '1G');

use app\Application;
use app\components\Config;
use app\components\Db;
use app\components\http\Response;
use app\components\Session;
use app\components\tj\AI;
use app\components\tj\Api;
use app\components\tj\Comment;

class TjController extends Controller {

	/** @var Api */
	protected $api;

	/**
	 *
	 * @throws \RuntimeException
	 */
	public function init(): void
	{
		parent::init();

		Session::start();

		$this->api = new Api(Config::load('api'));
	}

	public function actionIndex($page = 1): string
	{
		return 'tj index. Page:' . $page;
	}

	/**
	 * @return \app\components\tj\Notify[]|array
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function actionUpdates(): array
	{
		Application::$app->response->format = Response::FORMAT_JSON;

		$lastCheck = Db::i()->get('updates', '*');
		$checkDateTime = $lastCheck['last_parse_datetime'] ?? 0;

		$time = time();
		$replies = $this->api->getReplies();

		Db::i()->update('updates', ['last_parse_datetime' => $time]);

		if (!empty($replies)) {
			$newReplies = [];
			foreach ($replies as $reply) {
				if ($reply->date > $checkDateTime) {
					$newReplies[$reply->id] = $reply;
				}
			}
		}

		return $replies;
	}


	/**
	 * Проходимся по последним постам и публикуем комментарии
	 * После каждого опубликованного комментария выходим до следующего запуска
	 *
	 *
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 * @throws \RuntimeException
	 */
	public function actionPostcomments(): string
	{
		// Получаем 5 свежих постов
		$posts = $this->api->getTimeline('alaska', 'recent', 5);
		$parse_time = time();

		if (!empty($posts)) {

			// Проходимся по каждому
			foreach ($posts as $post) {

				// Получаем свои уже отправленные комменты к посту
				$myCommentsTexts = AI::getMyCommentsTexts($post->id);

				// Получаем свои ответы на чужие комменты
				$myRepliesTo = AI::getMyReplyesTo($post->id);

				// Получаем сохраненную информацию об этом посте
				$postInfo = Db::i()->get('posts', '*', ['postid' => $post->id]);

				// Если сохраненной информации нет, значит пост новый
				// Создаем запись об этом посте
				if (!$postInfo) {
					Db::i()->insert('posts', ['postid' => $post->id]);
					$postInfo['id'] = Db::i();
				}

				// ставим пометку о дате последнего парсинга
				Db::i()->update('posts', ['parse_datetime' => $parse_time, 'comments_count' => $post->commentsCount], ['postid' => $post->id]);

				// Получаем комменты
				$comments = $this->api->getComments($post->id);
				$commentsCount = \count($comments);

				// Если количество комментариев с момента последнего парсинга увеличилось
				if ($commentsCount > 0) {

					// Проходимся по каждому

					/** @var Comment $comment */
					foreach ($comments as $comment) {

						// Проверяем мой ли это коммент
						$isMyComment = $this->api->isMyComment($comment->author->id);

						// Если это не мой коммент и я на него еще не отвечал
						if (!$isMyComment && !\in_array($comment->id, $myRepliesTo)) {

							// Пытаемся триггернуться на коммент
							// Если получилось - шлем
							if ($reply = AI::trigger($post->id, $comment)) {
								$this->sendComment($post, $reply, $comment->id);

								return '';
							}

							// Если это ответ на мой коммент, пытаемся сгенерировать ответ
							if ($comment->replyTo && array_key_exists($comment->replyTo, $myCommentsTexts) && $reply = AI::getReply($post->id, $comment)) {

								$rate = $comment->author->id === 169200 ? 1 : null;
								$this->sendComment($post, $reply, $comment->id, $rate);

								return '';
							}
						}
					}
				}

				// Если у поста еще вообще нет комментариев или бот еще туда не комментил
				if (!$commentsCount || !\count($myCommentsTexts)) {
					// Пытаемся получить рандомный коммент
					if ($comment = AI::randomComment($post)) {
						$this->sendComment($post, $comment);

						return '';
					}
				}
			}
		}

		return '';
	}

	/**
	 * @param $post
	 * @param string $comment
	 * @param int $reply_to
	 *
	 * @param null $rate
	 *
	 * @throws \RuntimeException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	protected function sendComment($post, $comment = null, $reply_to = 0, $rate = null): void
	{
		$comment_text = '';
		$image_src = '';

		if (strpos($comment, 'photo:') === 0) {
			$image_src = str_replace('photo:', '', $comment);
		} else {
			$comment_text = $comment;
		}

		if ($reply_to) {

			$rate = $rate ?? $this->getRate($comment);

			if ($rate !== 0) {
				$this->api->rateComment($reply_to, $rate);
			}
		}

		$comment_id = $this->api->sendComment($post->id, $reply_to, $comment_text, $image_src);
		AI::savePostedComment($post, $comment_id, $comment, $reply_to);
	}

	protected function getRate(string $comment_text)
	{
		if (preg_match('#(минус|дизлайк|дизлойс)#ui', $comment_text)) {
			$rate = -1;
		} elseif (preg_match('#\b(лайк|лойс)#ui', $comment_text)) {
			$rate = 1;
		} else {
			$rate = random_int(-1, 1);
		}

		return $rate;
	}

	public function actionSearchcomments()
	{
		$term = $this->request->getQueryParams()['term']?? null;
		$comments =  [];

		if ($term) {
			$term = preg_replace('#([^a-zа-яë0-9_\-\s]+)#ui', '', $term);
			$query = Db::i()->query("SELECT * FROM archive WHERE comment_text LIKE '%{$term}%'");
			$comments = $query->fetchAll(\PDO::FETCH_ASSOC);
		}

		return $this->render('search', [
			'term' => $term,
			'models' => $comments,
		]);
	}

	/**
	 * @return string
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function actionIluhercomments()
	{
		set_time_limit(0);

		$userids = [2855, 51922, 67090, 67103, 67211, 68755, 120541, 120550, 120555, 122140, 122197, 122220, 122227, 122255, 128618, 128620, 129131, 131623, 131636, 133618, 134744, 160007, 169200,];

		$page_limit = 50;

		//Db::i()->query('TRUNCATE TABLE archive');

		$parsed = [];
		foreach ($userids as $i => $id) {
			$user = $this->api->makeRequest('GET', '/user/' . $id);
			$commentsCount = $user->counters->comments;
			$pages_count = (int)ceil($commentsCount / $page_limit);

			for ($page = 0; $page <= $pages_count; $page++) {
				$comments = $this->api->makeRequest('GET', '/user/' . $id . '/comments?count=' . $page_limit . '&offset=' . $page_limit * $page);
				sleep(1);
				foreach ($comments as $comment) {
					$data = ['user_id' => $comment->author->id, 'post_id' => $comment->entry->id, 'comment_text' => $comment->text, 'comment_id' => $comment->id, 'media' => json_encode($comment->media),];
					if ($comment->text !== '[Комментарий удалён]') {
						Db::i()->insert('archive', $data);
					}
				}
			}

			pr('parsed ' . $id . ' ; left: ' . (\count($userids) - $i + 1));
			$parsed[] = $id;
		}

		return 'parsed ' . implode(', ', $parsed);
	}
}