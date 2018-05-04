<?php

namespace app\components;

use RuntimeException;

class Component {

	/** @var static */
	protected static $instance;

	/**
	 * Component constructor.
	 *
	 * @param array $config
	 *
	 * @throws \RuntimeException
	 */
	public function __construct(array $config = [])
	{
		if (!empty($config)) {
			foreach ($config as $k => $value) {
				if (property_exists($this, $k)) {
					$this->{$k} = $value;
				} else {
					throw new RuntimeException('Class ' . static::class . ' has no property ' . $k);
				}
			}
		}

		$this->init();
	}

	protected function init()
	{

	}

	public static function lazyLoad($config)
	{
		/**
		 * @return Component
		 */
		return function () use ($config)
		{
			if (null === static::$instance) {
				static::$instance = new static($config);
			}

			return static::$instance;
		};
	}
}