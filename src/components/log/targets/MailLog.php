<?php

namespace app\components\log\targets;

use app\Application;
use app\components\log\handlers\PhpMailerHandler;
use app\components\log\Target;

class MailLog extends Target {

	public $mailTo = '';

	public function initHandler(): void
	{
		$this->handler = new PhpMailerHandler($this->level);
		$this->handler->setMailer(Application::$app->mailer);
		$this->handler->setMailTo($this->mailTo);
	}
}