<?php

namespace app\components;

use PHPMailer\PHPMailer\PHPMailer;

class Mailer extends Component {

	/** @var PHPMailer */
	protected $phpMailer;
	protected $from = [];

	/**
	 * Mailer constructor.
	 *
	 * @param array $config
	 *
	 * @throws \RuntimeException
	 * @throws \PHPMailer\PHPMailer\Exception
	 */
	public function __construct(array $config = [])
	{
		parent::__construct($config);

		$this->phpMailer = new PHPMailer(true);
		$this->phpMailer->setFrom($this->from[0],$this->from[1]);
		$this->phpMailer->isMail();
	}

	/**
	 * @param $mail_to
	 * @param $subject
	 * @param $body
	 *
	 * @return bool
	 * @throws \PHPMailer\PHPMailer\Exception
	 */
	public function sendMail($mail_to, $subject, $body): bool
	{
		$this->phpMailer->addAddress($mail_to);
		$this->phpMailer->Subject = $subject;
		$this->phpMailer->Body = $body;

		$this->phpMailer->isHTML(true);       // <=== call IsHTML() after $mail->Body has been set.
		return $this->phpMailer->send();
	}
}