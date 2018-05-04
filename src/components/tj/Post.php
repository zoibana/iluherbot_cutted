<?php

namespace app\components\tj;


/**
 * Class Post
 * @package app\components\tj
 *
 * @property int id
 * @property string title
 * @property int date
 * @property string dateRFC
 * @property string author
 * @property int type
 * @property string intro
 * @property string cover
 * @property array similar
 * @property array badges
 * @property int hitsCount
 * @property int commentsCount
 * @property int likes
 * @property int isFavorited
 * @property int isEnabledLikes
 * @property int isEnabledComments
 * @property int isAdvertisement
 */
class Post extends Entry
{
	public function getComments(): array
	{
		return $this->api->getComments($this->id);
	}
}