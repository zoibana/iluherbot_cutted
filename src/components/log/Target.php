<?php

namespace app\components\log;


use app\components\Component;
use Monolog\Handler\AbstractHandler;

abstract class Target extends Component {

	public $level = 0;
	public $formatter;

	/** @var AbstractHandler */
	protected $handler;

	public function init(): void
	{
		parent::init();

		$this->initHandler();

		if($this->formatter){
			$className = $this->formatter;
			$this->handler->setFormatter(new $className);
		}
	}

	abstract public function initHandler(): void;

	public function getHandler()
	{
		return $this->handler;
	}
}