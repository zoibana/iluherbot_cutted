<?php

namespace app\components\container;

/**
 * Class Container
 *
 * Виды значений контейнера:
 * 1. Инстанс класса - object
 * 2. Имя класса - string
 * 3. Анонимная функция - closure
 *
 *
 */
class Container {

	private $services = [];
	private $serviceStorage = [];

	public function set($id, $value):void
	{
		$this->services[$id] = $value;
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 * @throws ContainerEntryNotFoundException
	 * @throws ResolverUnknownTypeException
	 */
	public function get($id)
	{
		if (!$this->has($id)) {
			throw new ContainerEntryNotFoundException('Entry ' . $id . ' is not located in Container');
		}

		if(!array_key_exists($id, $this->serviceStorage)){
			$this->serviceStorage[$id] = $this->createService($id);
		}

		return $this->serviceStorage[$id];
	}

	public function has($id):bool
	{
		return array_key_exists($id, $this->services);
	}


	/**
	 * @param $name
	 *
	 * @return mixed
	 * @throws ResolverUnknownTypeException
	 */
	private function createService($name)
	{
		$service = $this->services[$name];

		if ($service instanceof \Closure) {
			$val = $service($this);
		} elseif (\is_object($service)) {
			$val = $service;
		} else {
			throw new ResolverUnknownTypeException('Service has unsupported type');
		}

		return $val;
	}
}