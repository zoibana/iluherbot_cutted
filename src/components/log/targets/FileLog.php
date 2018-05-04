<?php

namespace app\components\log\targets;

use app\components\log\Target;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;

class FileLog extends Target {

	public $file = '';
	public $maxFiles = 10;

	public function initHandler(): void
	{
		$this->handler = new RotatingFileHandler($this->file, $this->maxFiles, $this->level);
	}
}