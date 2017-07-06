<?php

namespace Hiraeth\ADR;

use Psr\Http\Message\StreamInterface as Stream;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 *
 */
abstract class AbstractResponder
{
	/**
	 *
	 */
	protected $data = array();


	/**
	 *
	 */
	protected $request = NULL;


	/**
	 *
	 */
	protected $response = NULL;


	/**
	 *
	 */
	protected $stream = NULL;


	/**
	 *
	 */
	abstract function __invoke();


	/**
	 *
	 */
	public function __construct(Response $response, Stream $stream)
	{
		$this->response = $response;
		$this->stream   = $stream;
	}


	/**
	 *
	 */
	public function render(Request $request, array $data = array())
	{
		$this->request = $request;
		$this->data    = $this->data + $data;

		$output = $this();

		if (!$output instanceof Response) {
			if (!$output instanceof Stream) {
				$this->stream->write((string) $output);

				$output = $this->stream;
			}

			$output = $this->response->withBody($output);
		}

		return $output;
	}
}
