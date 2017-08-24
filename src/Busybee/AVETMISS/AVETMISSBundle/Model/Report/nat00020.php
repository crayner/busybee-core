<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Model\Report;

use Symfony\Component\Process\Exception\InvalidArgumentException;

class nat00020 extends Report
{
	protected $reportName = 'nat00020';

	/**
	 * Execute
	 */
	public function execute($year)
	{
		$fileString   = '';
		$this->status = 'success';
		$this->getLastReport();
		$route  = '';
		$states = array(
			'NSW' => '01',
			'VIC' => '02',
			'QLD' => '03',
			'SA'  => '04',
			'WA'  => '05',
			'TAS' => '06',
			'NT'  => '07',
			'ACT' => '08',
			'OAT' => '09',
			'OS'  => '99'
		);


		$locations = $this->em->getRepository('BusybeeInstituteBundle:Campus')->findBy(array(), array('name' => 'ASC'));
		$locations = is_array($locations) ? $locations : array();
		foreach ($locations as $location)
		{
			foreach ($this->parameters['nat00020'] as $q => $w)
			{
				if ($w['setting'] === 'Org.Ext.Id')
					$value = $this->sm->get('Org.Ext.Id');
				else
					switch (str_replace('AVETMISS.Location.List.', '', $w['setting']))
					{
						case 'Identifier':
							$value = $location->getIdentifier();
							break;
						case 'Name':
							$value = $location->getName();
							break;
						case 'Postcode':
							$value = $location->getPostcode();
							break;
						case 'State':
							$value = isset($states[$location->getTerritory()]) ? $states[$location->getTerritory()] : '@@';
							break;
						case 'Locality':
							$value = $location->getLocality();
							break;
						case 'Country':
							$value = $location->getCountry();
							break;
						default:
							throw new InvalidArgumentException(sprintf("The NAT00020 parameter type %s has not been defined.", str_replace('AVETMISS.Location.List.', '', $w['setting'])));
					}

				$this->testValue($w, 'NAT00020', $q, $value, $route);

				$fileString .= $this->normalise($w['type'], $value, $w['length']);
			}
			$fileString .= "\r\n";
		}
		$this->fileString = $fileString;
		if (empty($locations))
		{
			$route             = new \stdClass();
			$route->path       = 'campus_manage';
			$route->parameters = array();
			$value             = '';
			$q                 = 'Report';
			$this->errors->addError($this->errors->createError('NAT00020', $q, 'report.error.empty', $value, $route));
			$this->status = 'danger';
		}
		else
			$this->writeFile();

		return $this;
	}
}
