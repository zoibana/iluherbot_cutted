<?php

namespace app\middleware;


use app\Application;
use app\components\ErrorHandler\ErrorProcessor;
use ErrorException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class ErrorHandlerMiddleware implements MiddlewareInterface {

	private $debug;
	private $error_mode_tolerant;
	private $error_mode_critical;

	/** @var LoggerInterface */
	private $logger;

	/** @var \Closure */
	private $errorRenderer;

	public function __construct(bool $debug, array $error_mode)
	{
		$this->debug = $debug;

		$this->error_mode_critical = $error_mode['critical'];
		$this->error_mode_tolerant = $error_mode['tolerant'];

		$this->initHandlers();
	}

	protected function initHandlers(): void
	{
		// Ставим повышенный уровень обработки ошибок, а дальше будем разбираться
		// как поступать с конкретным уровнем ошибки.
		error_reporting($this->error_mode_critical);

		if ($this->debug) {
			ini_set('display_errors', 1);
		} else {
			ini_set('display_startup_errors', 0);
			ini_set('display_errors', 0);
			ini_set('log_errors', 1);
		}

		ini_set('display_startup_errors', 0);
		ini_set('display_errors', 0);
		ini_set('log_errors', 1);

		set_error_handler(function ($severity, $message, $file, $line)
		{
			if (!(error_reporting() & $severity)) {
				// Этот код ошибки не включен в error_reporting,
				// так что пусть обрабатываются стандартным обработчиком ошибок PHP
				return false;
			}

			$error = new ErrorException($message, 0, $severity, $file, $line);

			// Если это "тихий" уровень ошибки
			if (($error->getSeverity() & $this->error_mode_tolerant) === $error->getSeverity()) {
				return $this->handleSilentError($error);
			}

			$this->handleCriticalError($error);

			return true;
		});

		set_exception_handler(function (\Throwable $exception) {
			$this->handleCriticalError($exception);
			return true;
		});

		register_shutdown_function(
			function () {
				$error = error_get_last();
				$exception = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
				$errorProc = new ErrorProcessor($exception);
				if ($errorProc->isFatal()) {
					$this->handleCriticalError($exception);
					return true;
				}

				return true;
			});
	}

	public function handleSilentError(\Throwable $error): bool
	{
		$errorProc = new ErrorProcessor($error);
		$message = $errorProc->getErrorLine();
		$this->logger->warning($message, ['error' => $error]);

		if($this->isDebug()){
			pr($message);
		}

		/* Не запускаем внутренний обработчик ошибок PHP */
		return true;
	}

	/**
	 * @param \Throwable $exception
	 *
	 * @return ResponseInterface
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 */
	public function handleCriticalError(\Throwable $exception): ResponseInterface
	{
		$errno = $exception instanceof ErrorException? $exception->getSeverity(): $exception->getCode();
		$message = $exception->getMessage();
		$errline = $exception->getLine();
		$errfile = $exception->getFile();

		$message = "[{$errno}] {$message} {$errfile}:{$errline}\n";

		$response = Application::$app->response;
		$response = $response->withStatus(500);

		if ($this->isDebug()) {
			$response->getBody()->write($message);
			return $response;
		}

		$this->logger->critical($message, ['exception' => $exception]);

		$body = $exception->getMessage();
		if ($this->errorRenderer) {
			$func = $this->errorRenderer;
			$body = $func($exception->getCode(), 'Error occurred');
		}

		$response->getBody()->write($body);

		return $response;
	}

	public function isDebug(): bool
	{
		return $this->debug;
	}

	public function setLogger(LoggerInterface $logger): void
	{
		$this->logger = $logger;
	}

	public function setErrorRenderer(\Closure $closure): void
	{
		$this->errorRenderer = $closure;
	}

	/**
	 * Process an incoming server request and return a response, optionally delegating
	 * response creation to a handler.
	 *
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface|null
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 * @throws \Throwable
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		try {
			return $handler->handle($request);
		} catch (\Throwable $exception) {
			return $this->handleCriticalError($exception);
		}
	}


}