<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Model\Report;

use Busybee\AVETMISS\AVETMISSBundle\Model\Report\Report;
use Symfony\Component\Process\Exception\InvalidArgumentException;

class nat00090 extends Report
{
	protected $reportName = 'nat00090';

	/**
	 * Execute
	 */
	public function execute($year)
	{
		$fileString   = '';
		$this->status = 'success';
		$this->getLastReport();


		$people = $this->em->getRepository('BusybeeAVETMISSBundle:Client')->createQueryBuilder('c')
			->where('c.disability > 0')
			->getQuery()
			->getResult();
		$people = is_array($people) ? $people : array();

		foreach ($people as $person)
		{
			$route             = new \stdClass();
			$route->path       = 'person_edit';
			$route->parameters = array('id' => $person->getPerson()->getId());

			foreach ($this->parameters[$this->reportName] as $q => $w)
			{
				switch ($q)
				{
					case 'clientID':
						$value = $person->getClientID();
						break;
					case 'disability':
						$value = $person->getDisability();
						break;
					default:
						throw new InvalidArgumentException('Handle ' . $q . ' I know not!!');
				}

				$this->testValue($w, strtoupper($this->reportName), $q, $value, $route);

				$fileString .= $this->normalise($w['type'], $value, $w['length']);
			}
			$fileString .= "\r\n";
		}
		$this->fileString = $fileString;

		if (empty($people))
		{
			$route             = new \stdClass();
			$route->path       = 'person_manage';
			$route->parameters = array();
			$value             = '';
			$q                 = 'Report';
			$this->errors->addError($this->errors->createError(strtoupper($this->reportName), $q, 'report.error.empty', $value, $route));
			$this->status = 'danger';
		}
		else
			$this->writeFile();

		return $this;
	}
}
