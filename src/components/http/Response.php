<?php

namespace app\components\http;


use Zend\Diactoros\Stream;

class Response  extends \Zend\Diactoros\Response {

	public const FORMAT_HTML = 'html';
	public const FORMAT_JSON = 'json';
	public const FORMAT_RAW = 'raw';

	public $format = self::FORMAT_HTML;

	/**
	 * @param $body
	 *
	 * @return Response
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 */
	public function withFormattedBody($body): Response
	{
		if($this->format === self::FORMAT_JSON){
			if(!\is_array($body)){
				throw new \RuntimeException('Return value of action for JSON format must be an array');
			}

			$body = \GuzzleHttp\json_encode($body);
		}

		if($this->format === self::FORMAT_HTML){
			if(!\is_string($body)){
				throw new \RuntimeException('Return value of action for HTML format must be a string');
			}
		}

		$stream = new Stream('php://memory', 'wb+');
		$stream->write($body);
		$stream->rewind();

		return $this->withBody($stream);
	}
}