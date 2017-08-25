<?php

namespace Hiraeth\ADR;

use Journey\Router as Router;
use Twig\Environment as Twig;
use Psr\Http\Message\StreamInterface as Stream;
use Psr\Http\Message\ResponseInterface as Response;
use Hiraeth;

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
	public function __construct(Hiraeth\Application $app, Response $response, Stream $stream, Twig $twig)
	{
		$this->app  = $app;
		$this->twig = $twig;

		parent::__construct($response, $stream);
	}


	/**
	 *
	 */
	public function __invoke()
	{
		if (!$this->path) {
			$request_path = $this->request->getUri()->getPath();

			if (substr($request_path, -1) == '/') {
				$this->path = '@pages' . $request_path . 'index.html';
			} else {
				$this->path = '@pages' . $request_path . '.html';
			}
		}

		try {
			$template = $this->twig->load($this->path);

		} catch (\Twig\Error\LoaderError $e) {
			if ($this->app->getEnvironment('DEBUG')) {
				throw $e;
			}

			return $this->response->withStatus(404);
		}

		$this->stream->write($template->render($this->data));

		return $this->response->withStatus(200)->withBody($this->stream);
	}
}

