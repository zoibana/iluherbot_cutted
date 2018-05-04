<?php

namespace app;

use app\components\Component;
use app\components\Config;
use app\components\container\Container;
use app\components\http\Response;
use app\components\log\Logger;
use app\components\Mailer;
use app\components\UrlManager;
use app\components\View;
use app\middleware\NotFoundMiddleware;
use Middlewares\TrailingSlash;
use Zend\Diactoros\Server;
use Zend\Stratigility\MiddlewarePipe;

/**
 * Class Application
 *
 * @package app
 *
 * @property View $view
 * @property Mailer $mailer
 * @property Response $response
 * @property Logger $logger
 * @property UrlManager $urls
 */
class Application {

	/** @var static */
	public static $app;
	private static $aliases = [];
	private $config = [];
	/** @var Container */
	private $container;
	private $pipeline;

	/**
	 * @param $aliasName
	 *
	 * @return mixed
	 * @throws \RuntimeException
	 */
	public static function getAlias($aliasName)
	{
		if (!array_key_exists($aliasName, self::$aliases)) {
			throw new \RuntimeException('Application has no alias with name ' . $aliasName);
		}

		return self::$aliases[$aliasName];
	}

	/**
	 * @param $configName
	 *
	 * @throws \Throwable
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 */
	public function run($configName): void
	{
		self::setAlias('@app', __DIR__);
		self::setAlias('@runtime', __DIR__ . DIRECTORY_SEPARATOR . 'runtime');

		self::$app = $this;

		$this->config = Config::load($configName);

		$this->container = new Container();

		$this->initComponents();
		$this->initPipeline();

		$server = Server::createServer([$this->pipeline, 'handle'], $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
		$server->listen(function ($req, $res)
		{
			return $res;
		});
	}

	public static function setAlias($aliasName, $path): void
	{
		self::$aliases[$aliasName] = rtrim($path, '\\/') . DIRECTORY_SEPARATOR;
	}

	/**
	 *
	 * @throws \InvalidArgumentException
	 */
	private function initComponents(): void
	{
		if (!empty($this->config['components'])) {
			foreach ((array)$this->config['components'] as $component => $componentConfig) {

				/** @var Component $class */
				$class = $componentConfig['class'];
				unset($componentConfig['class']);

				$this->container->set($component, function (Container $c) use ($class, $componentConfig)
				{
					return !empty($componentConfig) ? new $class($componentConfig) : new $class;
				});
			}
		}
	}

	/**
	 *
	 * @throws \Throwable
	 * @throws \InvalidArgumentException
	 */
	private function initPipeline(): void
	{
		$this->pipeline = new MiddlewarePipe();
		$routeMiddleware = new \app\middleware\RouteMiddleware($this->urls->getMatcher());
		$dispatchMiddleware = new \app\middleware\DispatcherMiddleware($this->response);

		$errorRenderer = function ($code, $message = ''): string
		{
			return $this->view->error($code, $message);
		};

		$errorHandlerMiddleware = new \app\middleware\ErrorHandlerMiddleware($this->config['debug_mode'], $this->config['error_mode']);
		$errorHandlerMiddleware->setLogger($this->logger);
		$errorHandlerMiddleware->setErrorRenderer($errorRenderer);

		$trailingSlashMiddleware = (new TrailingSlash(true))->redirect();

		$finalMiddleware = new NotFoundMiddleware($this->response, $errorRenderer);

		$this->pipeline->pipe($errorHandlerMiddleware);
		$this->pipeline->pipe($routeMiddleware);
		$this->pipeline->pipe($dispatchMiddleware);
		$this->pipeline->pipe($trailingSlashMiddleware);
		$this->pipeline->pipe($finalMiddleware);
	}

	/**
	 * @param $name
	 *
	 * @return mixed
	 * @throws \RuntimeException
	 * @throws components\container\ContainerEntryNotFoundException
	 * @throws components\container\ResolverUnknownTypeException
	 */
	public function __get($name)
	{
		if ($this->container->has($name)) {
			return $this->container->get($name);
		}
		throw new \RuntimeException('Application has no component called ' . $name);
	}

	public function __set($name, $value)
	{
		$this->container->set($name, $value);
	}

	public function __isset($name)
	{
		return $this->container->has($name);
	}
}