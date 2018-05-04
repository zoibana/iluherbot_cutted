<?php

namespace app\components;

class Session
{
	private static $is_started = false;

	public static function start(): void
	{
		if (!self::$is_started) {
			session_start();
		}
	}
}