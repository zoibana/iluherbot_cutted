<?php

namespace app\components;


use Aura\Router\Route;
use Aura\Router\RouterContainer;

class UrlManager extends Component {

	/** @var RouterContainer */
	protected $routeContainer;
	protected $urls = [];

	public function init()
	{
		$this->routeContainer = new RouterContainer();
		$this->loadUrls();
	}

	public function loadUrls()
	{
		$map = $this->routeContainer->getMap();

		foreach ($this->urls as $pattern => $handler) {

			$matched = preg_match_all('#\{([a-z_]+)(:([^\}]+))?\}#ui', $pattern, $matches);

			if ($matched) {

				$variables = [];
				$tokens = [];

				foreach ((array)$matches[0] as $k => $match) {
					$variable = $matches[1][$k];
					$token = $matches[3][$k];

					if($token) {
						$tokens[$variable] = $token;
					}

					$variables[$match] = '{'.$variable.'}';
				}

				foreach($variables as $str => $replace){
					$pattern = str_replace($str, $replace, $pattern);
				}

				/** @var Route $route */
				$route = $map->route($handler, $pattern);

				if(!empty($tokens)) {
					$route->tokens($tokens);
				}
			} else{
				$map->route($handler, $pattern);
			}
		}
	}

	public function getMatcher()
	{
		return $this->routeContainer->getMatcher();
	}

}