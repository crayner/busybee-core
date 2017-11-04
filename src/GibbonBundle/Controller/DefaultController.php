<?php

namespace GibbonBundle\Controller;

use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Symfony\Component\Intl\Intl;

class DefaultController extends BusybeeController
{
	public function indexAction()
	{
		$this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN', null, null);

		$gm = $this->get('gibbon.model.import_manager');

		$sql = "SELECT * FROM `gibbonPerson` ";

		$stmt = $gm->getGibbonManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$people = $stmt->fetchAll();

		$extra = [];

		$bom = $gm->getDefaultManager();

		foreach ($people as $gibbonPerson)
		{
			$person = $gm->getPerson($gibbonPerson);

			foreach ($gibbonPerson as $name => $value)
			{
				switch ($name)
				{
					case 'gibbonPersonID':
						$person->setImportIdentifier(intval($value));
						break;
					case 'title':
						if (!empty($value))
							$person->setHonorific($value);
						break;
					case 'surname':
						if (!empty($value))
							$person->setSurname($value);
						break;
					case 'firstName':
						if (!empty($value))
							$person->setFirstName($value);
						break;
					case 'preferredName':
						if (!empty($value))
							$person->setPreferredName($value);
						break;
					case 'officialName':
						if (!empty($value))
							$person->setOfficialName($value);
						break;
					case 'nameInCharacters':
						if (!empty($value))
							$person->setNameInCharacters($value);
						break;
					case 'gender':
						if (!empty($value))
							$person->setGender($value);
						break;
					case 'status':
						if (!empty($value) && method_exists($person, 'setStatus'))
							$person->setStatus($value);
						break;
					case 'dob':
						if (!empty($value))
						{
							$person->setDob(new \DateTime($value));
						}
						break;
					case 'email':
						if (!empty($value))
							$person->setEmail($value);
						break;
					case 'emailAlternate':
						if (!empty($value))
							$person->setEmail2($value);
						break;
					case 'address1':
						if (!empty($value))
							$extra[] = $gm->buildAddress($person, $gibbonPerson);
						break;
					case 'address1Country':
					case 'address1District':
						break;
					case 'address2':
						if (!empty($value))
							$extra[] = $gm->buildAddress($person, $gibbonPerson, '2');
						break;
					case 'address2Country':
					case 'address2District':
						break;
					case 'phone1':
						if (!empty($value))
							$extra[] = $gm->buildPhone($person, $gibbonPerson, '1');
						break;
					case 'phone1Type':
					case 'phone1CountryCode':
						break;
					case 'phone2':
						if (!empty($value))
							$extra[] = $gm->buildPhone($person, $gibbonPerson, '2');
						break;
					case 'phone2Type':
					case 'phone2CountryCode':
						break;
					case 'phone3':
						if (!empty($value))
							$extra[] = $gm->buildPhone($person, $gibbonPerson, '3');
						break;
					case 'phone3Type':
					case 'phone3CountryCode':
						break;
					case 'phone4':
						if (!empty($value))
							$extra[] = $gm->buildPhone($person, $gibbonPerson, '4');
						break;
					case 'phone4Type':
					case 'phone4CountryCode':
						break;
					case 'phone5':
						if (!empty($value))
							$extra[] = $gm->buildPhone($person, $gibbonPerson, '5');
						break;
					case 'phone5Type':
					case 'phone5CountryCode':
						break;
					case 'website':
						if (!empty($value))
							$person->setWebsite($value);
						break;
					case 'languageFirst':
						if (!empty($value))
							$person->setFirstLanguage($value);
						break;
					case 'languageSecond':
						if (!empty($value))
							$person->setSecondLanguage($value);
						break;
					case 'languageThird':
						if (!empty($value))
							$person->setThirdLanguage($value);
						break;
					case 'countryOfBirth':
						if (!empty($value))
						{
							$countries = Intl::getRegionBundle()->getCountryNames();
							$value     = array_search($value, $countries);
							if (!$value)
								$person->setCountryOfBirth($value);
						}
						break;
					case 'ethnicity':
						if (!empty($value))
							$person->setEthnicity($value);
						break;
					case 'citizenship1':
						if (!empty($value))
						{
							$countries = Intl::getRegionBundle()->getCountryNames();
							$value     = array_search($value, $countries);
							if (!$value)
								$person->setCitizenship1($value);
						}
						break;
					case 'citizenship1Passport':
						if (!empty($value))
							$person->setCitizenship1Passport($value);
						break;
					case 'citizenship1PassportScan':
						if (!empty($value))
							$person->setCitizenship1PassportScan($value);
						break;
					case 'citizenship2':
						if (!empty($value))
						{
							$countries = Intl::getRegionBundle()->getCountryNames();
							$value     = array_search($value, $countries);
							if (!$value)
								$person->setsetCitizenship2($value);
						}
						break;
					case 'citizenship2Passport':
						if (!empty($value))
							$person->setCitizenship2Passport($value);
						break;
					case 'citizenship2PassportScan':
						if (!empty($value))
							$person->setCitizenship2PassportScan($value);
						break;
					case 'birthCertificateScan':
						if (!empty($value))
							$person->setBirthCertificateScan($value);
						break;
					case 'religion':
						if (!empty($value))
							$person->setReligion($value);
						break;
					case 'nationalIDCardNumber':
						if (!empty($value))
							$person->setNationalIDCardNumber($value);
						break;
					case 'nationalIDCardScan':
						if (!empty($value))
							$person->setNationalIDCardScan($value);
						break;
					case 'residencyStatus':
						if (!empty($value))
							$person->setResidencyStatus($value);
						break;
					case 'visaExpiryDate':
						if (!empty($value))
						{
							$person->setVisaExpiryDate(new \DateTime($value));
						}
						break;
					default:
						dump([$name, $value]);
				}
			}
			dump($person);
			die();
		}

		return $this->render('GibbonBundle:Default:index.html.twig');
	}
}
