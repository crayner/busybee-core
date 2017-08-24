<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Model;

use Doctrine\ORM\EntityManager;

/**
 * Subject
 */
class ClientManager
{
	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;

		return $this;

	}

	public function handleRequest($data, $person)
	{

		/*
				$repo = $this->em->getRepository('BusybeeAVETMISSBundle:Client');
				$client = $repo->findOneByPerson($person->getId());
				$client->setPerson($this->em->getRepository('BusybeePersonBundle:Person')->findOneById($person->getId()));
				$data['person'] = $client->getPerson();
				$client->setClientID($data['clientID']);
				$client->setSchoolAttainment($data['schoolAttainment']);
				$client->setSchoolAttainmentYear($data['schoolAttainmentYear']);
				$client->setIndigenous($data['indigenous']);
				$client->setLanguage($data['language']);
				$client->setLabourForce($data['labourForce']);
				$client->setCountryBorn($data['countryBorn']);
				$client->setDisability($data['disability']);
				$client->setPriorEducation($data['priorEducation']);
				$client->setAtSchool(isset($data['atSchool']) && $data['atSchool'] ? 'Y' : 'N');
				$client->setEnglishProficiency(isset($data['englishProficiency']) ? $data['englishProficiency'] : null);
				$client->setUsi($data['usi']);

				$this->em->persist($client);
				$this->em->flush();
				$this->em->detach($client);
		*/
		return $data;
	}
}