<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Model\Report;

use Symfony\Component\Process\Exception\InvalidArgumentException;


class nat00080 extends Report
{
	protected $reportName = 'nat00080';

	/**
	 * Execute
	 */
	public function execute($year)
	{
		$fileString   = '';
		$this->status = 'success';
		$this->getLastReport();
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


		$people = $this->em->getRepository('BusybeeAVETMISSBundle:Client')->createQueryBuilder('c')
			->leftJoin('c.person', 'p')
			->leftJoin('p.address1', 'a')
			->leftJoin('a.locality', 'l')
			->getQuery()
			->getResult();
		$people = is_array($people) ? $people : array();

		foreach ($people as $person)
		{
			$route             = new \stdClass();
			$route->path       = 'person_edit';
			$route->parameters = array('id' => $person->getPerson()->getId());
			$address           = $person->getPerson()->getAddress1();
			$locality          = null !== $address ? $address->getLocality() : null;

			foreach ($this->parameters['nat00080'] as $q => $w)
			{
				switch ($q)
				{
					case 'clientID':
						$value = $person->getClientID();
						break;
					case 'name':
						$value = substr($person->getPerson()->formatName(), 0, 60);
						break;
					case 'schoolAttainment':
						$value = $person->getSchoolAttainment();
						break;
					case 'schoolAttainmentYear':
						$value         = $person->getSchoolAttainmentYear();
						$w['values']   = array();
						$w['values'][] = date('Y');
						for ($i = 1; $i < 105; $i++)
							$w['values'][] = date('Y', strtotime('-' . $i . ' Years'));
						break;
					case 'sex':
						$value = $person->getPerson()->getGender();
						break;
					case 'dob':
						$value = $person->getPerson()->getDob();
						break;
					case 'postCode':
						$value = null !== $locality ? $locality->getPostCode() : '';
						break;
					case 'indigenous':
						$value = $person->getIndigenous();
						break;
					case 'language':
						$value = $person->getLanguage();
						break;
					case 'labourForce':
						$value = $person->getLabourForce();
						break;
					case 'countryBorn':
						$value = $person->getCountryBorn();
						break;
					case 'disability':
						$value = intval($person->getDisability()) > 0 ? 'Y' : 'N';
						break;
					case 'priorEducation':
						$value = intval($person->getPriorEducation()) > 0 ? 'Y' : 'N';
						break;
					case 'atSchool':
						$value = $person->getAtSchool() ? 'Y' : 'N';
						break;
					case 'englishProficiency':
						$value = is_null($person->getEnglishProficiency()) ? null : $person->getEnglishProficiency();
						break;
					case 'locality':
						$value = null !== $locality ? $locality->getLocality() : '';
						break;
					case 'usi':
						$value = $person->getUsi();
						break;
					case 'state':
						$value = null !== $locality ? $states[$locality->getTerritory()] : '@@';
						break;
					case 'propertyName':
						$value = null !== $address ? $address->getPropertyName() : '';
						break;
					case 'building':
						$value = null !== $address ? trim($address->getBuildingType() . ' ' . $address->getBuildingNumber()) : '';
						break;
					case 'streetNumber':
						$value = null !== $address ? $address->getStreetNumber() : '';
						break;
					case 'streetName':
						$value = null !== $address ? $address->getStreetName() : '';
						if (intval(preg_match("#(PO|GPO|BOX|RMB)#", strtoupper($value))) > 0)
						{
							$this->errors->addError($this->errors->createError('NAT00080', $q, 'report.error.physicalAddress', $value, $route));
							$this->status = 'warning';
						}
						break;
					case 'sal1':
						$value = $person->getSal1();
						break;
					case 'sal2':
						$value = $person->getSal2();
						break;
					default:
						throw new InvalidArgumentException(sprintf("The NAT00080 parameter type %s has not been defined.", $q));
				}

				$this->testValue($w, 'NAT00080', $q, $value, $route);

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
			$this->errors->addError($this->errors->createError('NAT00080', $q, 'report.error.empty', $value, $route));
			$this->status = 'danger';
		}
		else
			$this->writeFile();

		return $this;
	}
}
