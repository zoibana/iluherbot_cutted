<?php

namespace app\components\ErrorHandler;

class ErrorProcessor {

	/** @var \Throwable */
	protected $exception;
	protected $levels = [
		0                   => 'EXCEPTION',
		E_ERROR             => 'ERROR',
		E_WARNING           => 'WARNING',
		E_PARSE             => 'PARSE ERROR',
		E_NOTICE            => 'NOTICE',
		E_CORE_ERROR        => 'CORE ERROR',
		E_CORE_WARNING      => 'CORE WARNING',
		E_COMPILE_ERROR     => 'COMPILE ERROR',
		E_COMPILE_WARNING   => 'COMPILE WARNING',
		E_USER_ERROR        => 'USER ERROR',
		E_USER_WARNING      => 'USER WARNING',
		E_USER_NOTICE       => 'USER NOTICE',
		E_STRICT            => 'STRICT',
		E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR',
		E_DEPRECATED        => 'DEPRECATED',
		E_USER_DEPRECATED   => 'USER DEPRECATED',
	];

	public function __construct(\Throwable $exception)
	{
		$this->exception = $exception;
	}

	public function isFatal(): bool
	{
		$errors = E_ERROR;
		$errors |= E_PARSE;
		$errors |= E_CORE_ERROR;
		$errors |= E_CORE_WARNING;
		$errors |= E_COMPILE_ERROR;
		$errors |= E_COMPILE_WARNING;

		return ($this->getLevel() & $errors) > 0;
	}

	public function getLevel(): int
	{
		return $this->exception instanceof \ErrorException ? $this->exception->getSeverity() : $this->exception->getCode();
	}

	public function getLevelName(): string
	{
		return $this->levels[$this->getLevel()] ?? 'UNKNOWN ERROR';
	}

	public function getErrorLine(): string
	{
		return $this->getLevelName() . ": [{$this->getLevel()}] {$this->exception->getMessage()} {$this->exception->getFile()}:{$this->exception->getLine()}\n";
	}

	public function displayError($message): void
	{
		if (PHP_SAPI !== 'cli') {
			$message = nl2br($message);
		}

		print $message;
	}
}