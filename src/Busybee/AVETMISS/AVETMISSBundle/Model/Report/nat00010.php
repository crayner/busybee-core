<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Model\Report;

use Busybee\AVETMISS\AVETMISSBundle\Model\Report\Report;

class nat00010 extends Report
{
	protected $reportName = 'nat00010';

	/**
	 * Execute
	 */
	public function execute($year)
	{
		$this->year = $year;
		$this->getLastReport();
		$fileString   = '';
		$this->status = 'success';
		$states       = array(
			'NSW' => '01',
			'VIC' => '02',
			'QLD' => '03',
			'SA'  => '04',
			'WA'  => '05',
			'TAS' => '06',
			'NT'  => '07',
			'ACT' => '08',
			'OAT' => '09'
		);

		foreach ($this->parameters['nat00010'] as $q => $w)
		{
			$value             = $this->sm->get($w['setting']);
			$route             = new \stdClass();
			$route->path       = 'avetmiss_nat00010_settings';
			$route->parameters = array();

			$this->testValue($w, 'NAT00010', $q, $value, $route);

			if ($w['setting'] == 'Org.Name' && $value == 'Busybee Institute')
			{
				$this->errors->addError($this->errors->createError('NAT00010', $q, 'report.error.default', $value, $route));
				$this->status = 'danger';
			}
			if ($w['setting'] == 'Org.Physical.Territory' && !empty($states[$value]))
				$value = $states[$value];
			elseif ($w['setting'] == 'Org.Physical.Territory' && empty($states[$value]))
			{
				$this->errors->addError($this->errors->createError('NAT00010', $q, 'report.error.invalid', $value, $route));
				$this->status = 'danger';
			}
			if ($w['setting'] == 'AVETMISS.Org.Type')
			{
				$typeList = $this->container->getParameter('AVETMISS')['Org_Type_List'];
				$valid    = false;
				foreach ($typeList as $x)
					foreach ($x as $y)
						if ($y == $value)
						{
							$valid = true;
							break;
						}
				if (!$valid)
				{
					$this->errors->addError($this->errors->createError('NAT00010', $q, 'report.error.invalid', $value, $route));
					$this->status = 'danger';
				}
			}

			$fileString .= $this->normalise($w['type'], $value, $w['length']);
		}
		$this->fileString = $fileString . "\r\n";

		$this->writeFile();

		return $this;
	}
}
