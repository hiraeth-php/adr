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
		$template = NULL;

		if (!$this->path) {
			$request_path = $this->request->getUri()->getPath();

			if (substr($request_path, -1) == '/') {
				$this->path = '@pages' . $request_path . 'index.html';
				$alt_path   = substr($request_path, 0, -1);
			} else {
				$this->path = '@pages' . $request_path . '.html';
				$alt_path   = $request_path . '/';
			}
		}

		try {
			$template = $this->twig->load($this->path);

		} catch (\Twig\Error\LoaderError $e) {
			if ($this->app->getEnvironment('DEBUG')) {
				throw $e;
			}
		}

		if (!$template) {
			if (substr($alt_path, -1) == '/') {
				$this->path = '@pages' . $alt_path . 'index.html';
			} else {
				$this->path = '@pages' . $alt_path . '.html';
			}

			try {
				$template = $this->twig->load($this->path);
				$redirect = $this->request->getURI()->withPath($alt_path);

				return $this->response->withStatus(301)->withHeader('Location', $redirect);

			} catch (\Twig\Error\LoaderError $e) {
				return $this->response->withStatus(404);
			}
		}

		$this->stream->write($template->render($this->data));

		return $this->response
			->withStatus(200)
			->withHeader('Content-Type', 'text/html; charset=utf-8')
			->withBody($this->stream);
	}
}
