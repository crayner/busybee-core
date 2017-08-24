<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Model\Report;

use Symfony\Component\Process\Exception\InvalidArgumentException;

class nat00060 extends Report
{
	protected $reportName = 'nat00060';

	/**
	 * Execute
	 */
	public function execute($year)
	{
		$fileString   = '';
		$this->status = 'success';
		$this->getLastReport();


		$subjects = $this->em->getRepository('BusybeeAVETMISSBundle:Subject')->createQueryBuilder('a')
			->leftJoin('a.subject', 's')
			->orderBy('s.name', 'ASC')
			->getQuery()
			->getResult();
		$subjects = is_array($subjects) ? $subjects : array();

		foreach ($subjects as $subject)
		{
			foreach ($this->parameters['nat00060'] as $q => $w)
			{
				switch ($q)
				{
					case 'identifier':
						$value = $subject->getIdentifier();
						break;
					case 'name':
						$value = $subject->getSubject()->getName();
						break;
					case 'nominalHours':
						$value = intval($subject->getNominalHours());
						break;
					case 'FOEIdentifier':
						$value = intval($subject->getFOEIdentifier());
						break;
					case 'VETFlag':
						$value = $subject->getVETFlag() ? 'Y' : 'N';
						break;
					case 'subjectFlag':
						$value = $subject->getSubjectFlag() ? 'C' : 'M';
						break;
					default:
						throw new InvalidArgumentException(sprintf("The NAT00060 parameter type %s has not been defined.", $q));
				}
				$route             = new \stdClass();
				$route->path       = 'avetmiss_subject_manage';
				$route->parameters = array('id' => $subject->getId());

				$this->testValue($w, 'NAT00060', $q, $value, $route);

				$fileString .= $this->normalise($w['type'], $value, $w['length']);
			}
			$fileString .= "\r\n";
		}
		$this->fileString = $fileString;
		if (empty($subjects))
		{
			$route             = new \stdClass();
			$route->path       = 'avetmiss_subject_manage';
			$route->parameters = array();
			$value             = '';
			$q                 = 'Report';
			$this->errors->addError($this->errors->createError('NAT00060', $q, 'report.error.empty', $value, $route));
			$this->status = 'danger';
		}
		else
			$this->writeFile();

		return $this;
	}
}
