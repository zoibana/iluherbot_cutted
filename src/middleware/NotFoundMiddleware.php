<?php

namespace app\middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class NotFoundMiddleware implements MiddlewareInterface {

	/** @var ResponseInterface */
	protected $response;

	/** @var \Closure */
	protected $errorRenderer;

	public function __construct(ResponseInterface $response, \Closure $errorRenderer)
	{
		$this->response = $response;
		$this->errorRenderer = $errorRenderer;
	}

	/**
	 * Process an incoming server request and return a response, optionally delegating
	 * response creation to a handler.
	 *
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 * @throws \Throwable
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$code = 404;
		$this->response = $this->response->withStatus($code);
		$func = $this->errorRenderer;
		$this->response->getBody()->write($func($code, 'Page not found'));

		return $this->response;
	}
}