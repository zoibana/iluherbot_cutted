<?php

namespace app\components\log\handlers;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class PhpMailerHandler extends AbstractProcessingHandler {

	/** @var \app\components\Mailer */
	protected $phpMailer;
	protected $mailTo = '';

	public function setMailTo(string $mail): void
	{
		$this->mailTo = $mail;
	}

	public function setMailer($mailer): void
	{
		$this->phpMailer = $mailer;
	}

	/**
	 * @param array $record
	 *
	 * @return bool
	 * @throws \PHPMailer\PHPMailer\Exception
	 */
	protected function write(array $record): bool
	{
		$subject = $record['channel'] . '-' . $record['level_name'] . ' ' . $record['message'];
		return $this->phpMailer->sendMail($this->mailTo, $subject, $record['formatted']);
	}
}