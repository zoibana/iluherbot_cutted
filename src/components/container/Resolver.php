<?php

namespace app\components\container;


class Resolver {

	/** @var Container */
	private $container;
	private $resolvedServices = [];

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * @param string $serviceName
	 * @param $service
	 *
	 * @return mixed
	 * @throws ResolverUnknownTypeException
	 */
	public function resolve(string $serviceName, $service)
	{
		if (!array_key_exists($serviceName, $this->resolvedServices)) {

			if ($service instanceof \Closure) {
				$val = $service($this->container);
			} elseif (\is_object($service)) {
				$val = $service;
			} else {
				throw new ResolverUnknownTypeException('Service has unsupported type');
			}

			$this->resolvedServices[$serviceName] = $val;
		}

		return $this->resolvedServices[$serviceName];
	}
}