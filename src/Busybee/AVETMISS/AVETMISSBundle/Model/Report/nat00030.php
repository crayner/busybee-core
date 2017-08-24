<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Model\Report;

use Symfony\Component\Process\Exception\InvalidArgumentException;


class nat00030 extends Report
{
	protected $reportName = 'nat00030';

	/**
	 * Execute
	 */
	public function execute($year)
	{
		$fileString   = '';
		$this->status = 'success';
		$this->getLastReport();


		$courses = $this->em->getRepository('BusybeeAVETMISSBundle:Course')->createQueryBuilder('c')
			->leftJoin('c.course', 'p')
			->orderBy('p.name', 'ASC')
			->getQuery()
			->getResult();
		$courses = is_array($courses) ? $courses : array();

		foreach ($courses as $course)
		{
			foreach ($this->parameters['nat00030'] as $q => $w)
			{
				switch ($q)
				{
					case 'identifier':
						$value = $course->getIdentifier();
						break;
					case 'name':
						$value = $course->getCourse()->getName();
						break;
					case 'nominalHours':
						$value = intval($course->getNominalHours());
						break;
					case 'recognitionIdentifier':
						$value = intval($course->getRecognitionIdentifier());
						break;
					case 'levelEducationIdentifier':
						$value = intval($course->getLevelEducationIdentifier());
						break;
					case 'FOEIdentifier':
						$value = intval($course->getFOEIdentifier());
						break;
					case 'ANZSCOIdentifier':
						$value = $course->getANZSCOIdentifier();
						break;
					case 'VETFlag':
						$value = $course->getVETFlag() ? 'Y' : 'N';
						break;
					default:
						throw new InvalidArgumentException(sprintf("The NAT00030 parameter type %s has not been defined.", $q));
				}
				$route             = new \stdClass();
				$route->path       = 'avetmiss_program_manage';
				$route->parameters = array('id' => $course->getId());


				$this->testValue($w, 'NAT00030', $q, $value, $route);

				$fileString .= $this->normalise($w['type'], $value, $w['length']);
			}
			$fileString .= "\r\n";
		}
		$this->fileString = $fileString;
		if (empty($courses))
		{
			$route             = new \stdClass();
			$route->path       = 'avetmiss_program_manage';
			$route->parameters = array();
			$value             = '';
			$q                 = 'Report';
			$this->errors->addError($this->errors->createError('NAT00030', $q, 'report.error.empty', $value, $route));
			$this->status = 'danger';
		}
		else
			$this->writeFile();

		return $this;
	}
}
