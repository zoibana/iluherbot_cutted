<?php

namespace app\middleware;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

class DispatcherMiddleware implements MiddlewareInterface {

	private $response;

	public function __construct(ResponseInterface $response)
	{
		$this->response = $response;
	}

	/**
	 * Process an incoming server request and return a response, optionally delegating
	 * response creation to a handler.
	 *
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 *
	 * @throws \ReflectionException
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		/** @var \Aura\Router\Route $route */
		$route = $request->getAttribute('route');
		$name = $route->name;
		$path = trim($route->path, '/');

		$parameters = [];
		if (preg_match_all('#\{([^\}]+)\}#', $path, $matches)) {
			foreach ((array)$matches[1] as $param) {
				$parameters[$param] = $request->getAttribute($param);
			}
		}

		if (!empty($parameters)) {
			foreach ($parameters as $param => $value) {
				$name = str_replace('{' . $param . '}', $value, $name);
			}
		}

		$pathParts = explode('/', $name);
		$controllerName = $pathParts[0];
		$actionName = $pathParts[1] ?? 'index';

		$actionMethodName = 'action' . ucfirst($actionName);

		$controllerClassName = '\app\controllers\\' . ucfirst($controllerName) . 'Controller';

		$controller = new $controllerClassName($request, $this->response);

		if (method_exists($controller, $actionMethodName)) {
			$reflection = new \ReflectionClass($controllerClassName);
			$methodReflection = $reflection->getMethod($actionMethodName);
			$methodParameters = $methodReflection->getParameters();

			$passParams = [];
			if (!empty($methodParameters)) {
				foreach ($methodParameters as $parameter) {
					$name = $parameter->getName();
					if (array_key_exists($name, $parameters)) {
						$passParams[$name] = $parameters[$name];
					} else {
						if (!$parameter->isOptional()) {
							throw new RuntimeException('Action ' . $actionName . ' has required parameter ' . $name);
						}
					}
				}
			}

			$responseBody = \call_user_func_array([$controller, $actionMethodName], $passParams);

			return $this->response->withFormattedBody($responseBody);
		}

		return $handler->handle($request);

		//		$message = 'action ' . $controllerClassName . '\\' . $actionMethodName . ' is not found';
		//		throw HttpErrorException::create(404, [ 'request' => $request,'message' => $message]);

	}
}