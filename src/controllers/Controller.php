<?php

namespace app\controllers;


use app\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Controller {

	protected $request;
	protected $response;
	protected $id;
	protected $view;

	/**
	 * Controller constructor.
	 *
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 *
	 * @throws \ReflectionException
	 */
	public function __construct(ServerRequestInterface $request, ResponseInterface $response) {

		$this->request = $request;
		$this->response = $response;

		$this->view = Application::$app->view;

		$className = strtolower((new \ReflectionClass($this))->getShortName());
		$this->id = str_replace('controller', '', $className);

		$this->init();
	}

	public function init(){

	}

	public function actionError($exception){
		return $this->render('/error/error',[
			'exception' => $exception,
		]);
	}

	public function redirect($url, $code = 303): bool
	{
		header("Location: $url", true, $code);
		exit;
	}

	public function render($template, array $params= [])
	{
		if(strpos('/', $template) !== 0){
			$template = $this->id . DIRECTORY_SEPARATOR . $template;
		}

		return $this->view->renderTemplate($template, $params);
	}
}