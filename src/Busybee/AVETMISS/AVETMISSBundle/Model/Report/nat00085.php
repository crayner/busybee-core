<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Model\Report;

use Busybee\AVETMISS\AVETMISSBundle\Model\Report\Report;

class nat00085 extends Report
{
	protected $reportName = 'nat00085';

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
			$address1          = $person->getPerson()->getAddress1();
			$locality1         = null !== $address1 ? $address1->getLocality() : null;

			foreach ($this->parameters[$this->reportName] as $q => $w)
			{
				switch ($q)
				{
					case 'clientID':
						$value = $person->getClientID();
						break;
					case 'clientTitle':
						$value = $person->getPerson()->getTitle();
						break;
					case 'firstName':
						$value = $person->getPerson()->getFirstName();
						break;
					case 'familyName':
						$value = $person->getPerson()->getSurname();
						break;
					case 'propertyName':
						$value = null !== $address1 ? $address1->getPropertyName() : '';
						break;
					case 'building':
						$value1 = null !== $address1 ? $address1->getBuildingType() : '';
						$value2 = null !== $address1 ? $address1->getBuildingNumber() : '';
						$value  = trim($value1 . ' ' . $value2);
						break;
					case 'streetNumber':
						$streetNumber = $value = null !== $address1 ? $address1->getStreetNumber() : '';
						break;
					case 'streetName':
						$streetName = $value = null !== $address1 ? $address1->getStreetName() : '';
						break;
					case 'deliveryBox':
						$value = '';
						if (!is_null($person->getPerson()->getAddress2()))
						{
							$value = intval(preg_match("#(PO|GPO|BOX|RMB)#", strtoupper($person->getPerson()->getAddress2()->getStreetName()))) > 0 ? $person->getPerson()->getAddress2()->getStreetName() : '';
						}
						if (empty(trim($streetNumber . $streetName . $value))) unset($w['blank']);
						break;
					case 'locality':
						$value = is_null($person->getPerson()->getAddress2()) ? $person->getPerson()->getAddress1()->getLocality()->getLocality() : $person->getPerson()->getAddress2()->getLocality()->getLocality();
						break;
					case 'postCode':
						$value = is_null($person->getPerson()->getAddress2()) ? $person->getPerson()->getAddress1()->getLocality()->getPostCode() : $person->getPerson()->getAddress2()->getLocality()->getPostCode();
						break;
					case 'state':
						$value = is_null($person->getPerson()->getAddress2()) ? $person->getPerson()->getAddress1()->getLocality()->getTerritory() : $person->getPerson()->getAddress2()->getLocality()->getTerritory();
						$value = isset($states[$value]) ? $states[$value] : $w['default'];
						break;
					case 'homePhone':
						$value  = '';
						$phones = $person->getPerson()->getPhone();
						foreach ($phones as $phone)
							if ($phone->getPhoneType() == 'Home')
							{
								$value = $phone->getPhoneNumber();
								break;
							}
						break;
					case 'workPhone':
						$value  = '';
						$phones = $person->getPerson()->getPhone();
						foreach ($phones as $phone)
							if ($phone->getPhoneType() == 'Work')
							{
								$value = $phone->getPhoneNumber();
								break;
							}
						break;
					case 'mobilePhone':
						$value  = '';
						$phones = $person->getPerson()->getPhone();
						foreach ($phones as $phone)
							if ($phone->getPhoneType() == 'Mobile')
							{
								$value = $phone->getPhoneNumber();
								break;
							}
						break;
					case 'email':
						$value = $person->getPerson()->getEmail();
						break;
					default:
						throw new \Exception('Handle ' . $q . ' I know not!!');
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
