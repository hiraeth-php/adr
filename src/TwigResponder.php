<?php

namespace Hiraeth\ADR;

use Journey\Router as Router;
use Twig\Environment as Twig;
use Psr\Http\Message\StreamInterface as Stream;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
class TwigResponder extends AbstractResponder
{
	/**
	 *
	 */
	protected $path = NULL;


	/**
	 *
	 */
	protected $twig = NULL;


	/**
	 *
	 */
	public function __construct(Response $response, Stream $stream, Twig $twig)
	{
		parent::__construct($response, $stream);

		$this->twig = $twig;
	}


	/**
	 *
	 */
	public function __invoke()
	{
		if (!$this->path) {
			$this->path = $this->request->getUri()->getPath();
		}

		if (substr($this->path, -1) == '/') {
			$template = '@pages' . $this->path . 'index.html';
		} else {
			$template = '@pages' . $this->path . '.html';
		}

		try {
			$template  = $this->twig->load($template);
			$byte_size = $this->stream->write($template->render($this->data));

			return $this->response->withStatus(200)->withBody($this->stream);

		} catch (\Twig\Error\LoaderError $e) {
			return $this->response->withStatus(404);
		}
	}
}
