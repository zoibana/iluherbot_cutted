<?php

namespace app\components\tj;

/**
 * Class Notify
 *
 * @package app\components\tj
 *
 * @property integer $id
 * @property integer $type
 * @property integer $date
 * @property string $dateRFC
 * @property string $text
 * @property string $url
 * @property string $icon
 */
class Notify extends Entry {

	public const TYPE_LIKE = 2;
	public const TYPE_REPLY = 4;

	public function isReply(): bool
	{
		return (int)$this->type === self::TYPE_REPLY;
	}

	public function isLike(): bool
	{
		return (int)$this->type === self::TYPE_LIKE;
	}

	public function getCommentIdRepliedTo(): int
	{
		$matches = [];
		if (preg_match('#-(\d+)$#', $this->url, $matches)) {
			return (int)$matches[1];
		}

		return 0;
	}

	public function getPostId()
	{
		$matches = [];
		if (preg_match('#\/(\d+)-([a-z0-9\-_]+)\/?$#', $this->url, $matches)) {
			return (int)$matches[1];
		}

		return 0;
	}
}