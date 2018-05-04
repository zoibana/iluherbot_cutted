<?php

namespace app\components\log;


use app\components\Component;
use Psr\Log\LoggerInterface;

class Logger extends Component implements LoggerInterface {

	/** @var \Monolog\Logger */
	protected $monolog;

	protected $targets = [];
	protected $processors = [];

	public function init(): void
	{
		parent::init();

		$this->monolog = new \Monolog\Logger('default');

		foreach ($this->targets as $k => $target) {
			$targetClass = $target['class'];
			unset($target['class']);

			/** @var Target $targetInst */
			$targetInst = new $targetClass($target);

			$this->monolog->pushHandler($targetInst->getHandler());
		}

		if (!empty($this->processors)) {
			foreach ($this->processors as $processor) {
				$processorInstance = \is_string($processor) ? new $processor() : $processor;
				$this->monolog->pushProcessor($processorInstance);
			}
		}
	}

	public function log($level, $message, array $context = []): bool
	{
		return $this->monolog->log($level, $message, $context);
	}

	/**
	 * System is unusable.
	 *
	 * @param string $message
	 * @param array $context
	 *
	 * @return bool
	 */
	public function emergency($message, array $context = array()): bool
	{
		return $this->monolog->emergency($message, $context);
	}

	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should
	 * trigger the SMS alerts and wake you up.
	 *
	 * @param string $message
	 * @param array $context
	 *
	 * @return bool
	 */
	public function alert($message, array $context = array()): bool
	{
		return $this->monolog->alert($message, $context);
	}

	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param string $message
	 * @param array $context
	 *
	 * @return bool
	 */
	public function critical($message, array $context = array()): bool
	{
		return $this->monolog->critical($message, $context);
	}

	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored.
	 *
	 * @param string $message
	 * @param array $context
	 *
	 * @return bool
	 */
	public function error($message, array $context = array()): bool
	{
		return $this->monolog->error($message, $context);
	}

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
	 * that are not necessarily wrong.
	 *
	 * @param string $message
	 * @param array $context
	 *
	 * @return bool
	 */
	public function warning($message, array $context = array()): bool
	{
		return $this->monolog->warning($message, $context);
	}

	/**
	 * Normal but significant events.
	 *
	 * @param string $message
	 * @param array $context
	 *
	 * @return void
	 */
	public function notice($message, array $context = array()): void
	{
		$this->monolog->notice($message, $context);
	}

	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 *
	 * @param string $message
	 * @param array $context
	 *
	 * @return bool
	 */
	public function info($message, array $context = array()): bool
	{
		return $this->monolog->info($message, $context);
	}

	/**
	 * Detailed debug information.
	 *
	 * @param string $message
	 * @param array $context
	 *
	 * @return bool
	 */
	public function debug($message, array $context = array()): bool
	{
		return $this->monolog->debug($message, $context);
	}
}