<?php

namespace Busybee\PaginationBundle\Model ;

use Symfony\Component\HttpFoundation\Request ;

interface PaginationManagerInterface
{
	public function buildQuery();
}