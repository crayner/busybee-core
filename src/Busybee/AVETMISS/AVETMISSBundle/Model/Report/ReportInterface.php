<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Model\Report;

interface ReportInterface
{
	/**
	 * Execute
	 *
	 * @param    string $year
	 */
	public function execute($year);

	/**
	 * retrieve Last Report
	 *
	 * @param    string $year
	 */
	public function retrieveLastReport($year);
}
