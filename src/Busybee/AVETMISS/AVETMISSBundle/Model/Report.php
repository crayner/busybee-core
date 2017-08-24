<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Model;

class Report
{
	/**
	 * Construct
	 */
	public function __construct()
	{
		$this->setYear(date('Y', strtotime('now')));
	}
}