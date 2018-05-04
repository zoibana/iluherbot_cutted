<?php

namespace app\middleware;

use app\Application;
use Middlewares\HttpErrorException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

class RouteMiddleware implements MiddlewareInterface {

	private $router;

	public function __construct(\Aura\Router\Matcher $router)
	{
		$this->router = $router;
	}

	/**
	 * Process an incoming server request and return a response, optionally delegating
	 * response creation to a handler.
	 *
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 * @throws \InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		if ($route = $this->router->match($request)) {
			foreach ($route->attributes as $attribute => $value) {
				$request = $request->withAttribute($attribute, $value);
			}

			return $handler->handle($request->withAttribute('route', $route));
		}

		return $handler->handle($request);
	}
}