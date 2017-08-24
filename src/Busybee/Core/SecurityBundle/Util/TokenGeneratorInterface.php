<?php

namespace Busybee\Core\SecurityBundle\Util;

interface TokenGeneratorInterface
{
	/**
	 * @return string
	 */
	public function generateToken();
}
