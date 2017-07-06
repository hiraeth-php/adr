<?php

namespace Hiraeth\ADR;

use Journey\Router as Router;

/**
 *
 */
abstract class AbstractAction
{
	/**
	 *
	 */
	abstract public function __invoke(Router $request);
}
