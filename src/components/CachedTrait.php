<?php

namespace app\components;


trait CachedTrait {

	protected $_cached = [];
	protected static $_static_cached = [];

	protected function getCached($key, callable  $getter){
		if(!array_key_exists($key, $this->_cached)){
			$this->_cached[$key] = $getter();
		}

		return $this->_cached[$key];
	}

	protected static function getStaticCached($key, callable  $getter){
		if(!array_key_exists($key, self::$_static_cached)){
			self::$_static_cached[$key] = $getter();
		}

		return self::$_static_cached[$key];
	}
}