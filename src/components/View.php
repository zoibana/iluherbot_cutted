<?php

namespace app\components;


class View extends Component {

	public $layout = 'index';
	public $viewsPath = 'views';
	public $templateExtension = 'php';
	public $errorTemplate = '/error/error';
	public $exceptionTemplate = '/error/exception';

	/**
	 * @param $tplName
	 * @param array $params
	 *
	 * @return null|string
	 * @throws \RuntimeException
	 * @throws \Throwable
	 */
	public function renderTemplate($tplName, array $params = []): string
	{
		$tplFileName = $this->viewsPath . DIRECTORY_SEPARATOR . $tplName . '.' . $this->templateExtension;
		if (!is_file($tplFileName)) {
			throw new \RuntimeException('Template ' . $tplFileName . ' is not found');
		}

		return (string) $this->renderPhpFile($tplFileName, $params);
	}

	public function renderPhpFile($file, array $params = []): ?string
	{
		$obInitialLevel = ob_get_level();

		ob_start();
		ob_implicit_flush(false);

		extract($params, EXTR_OVERWRITE);

		try {

			require $file;
			return ob_get_clean();

		} catch (\Exception $e) {
			while (ob_get_level() > $obInitialLevel) {
				if (!@ob_end_clean()) {
					ob_clean();
				}
			}

			throw $e;
		} catch (\Throwable $e) {
			while (ob_get_level() > $obInitialLevel) {
				if (!@ob_end_clean()) {
					ob_clean();
				}
			}

			throw $e;
		}
	}

	/**
	 * @param int $code
	 * @param string $message
	 *
	 * @return string
	 * @throws \Throwable
	 */
	public function error(int $code, string $message = ''): string
	{
		$exception = new \Exception($message, $code);
		return $this->renderTemplate($this->errorTemplate, ['exception' => $exception]);
	}

	/**
	 * @param \Throwable $exception
	 *
	 * @return null|string
	 * @throws \Throwable
	 */
	public function exception(\Throwable $exception): ?string
	{
		return $this->renderTemplate($this->exceptionTemplate, ['exception' => $exception]);
	}
}