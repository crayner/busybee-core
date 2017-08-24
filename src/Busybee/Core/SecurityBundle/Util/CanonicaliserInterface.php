<?php

namespace Busybee\Core\SecurityBundle\Util;

interface CanonicaliserInterface
{
	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public function canonicalise($string);
}
