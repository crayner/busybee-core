<?php

namespace Busybee\People\FamilyBundle\Model;

use Busybee\People\FamilyBundle\Entity\CareGiver;
use Busybee\People\FamilyBundle\Entity\Family;
use Busybee\People\PersonBundle\Entity\Person;
use Busybee\People\PersonBundle\Model\PersonManager;
use Busybee\People\StudentBundle\Entity\Student;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Persistence\ObjectManager;

class FamilyManager
{
	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * @var ObjectManager
	 */
	private $objectManager;

	/**
	 * @var PersonManager
	 */
	private $personManager;

	/**
	 * PersonManager constructor.
	 *
	 * @param SettingManager $settingManager
	 */
	public function __construct(SettingManager $settingManager, ObjectManager $objectManager, PersonManager $personManager)
	{
		$this->settingManager = $settingManager;
		$this->objectManager  = $objectManager;
		$this->personManager  = $personManager;
	}

	/**
	 * @param $data
	 *
	 * @return null|string
	 */
	public function generateFamilyName($data)
	{
		if (empty($data['careGivers']) || empty($data['careGivers'][0]['person']))
			return '';

		$cgr = $this->objectManager->getRepository(Person::class);

		$cg1 = $cgr->find($data['careGivers'][0]['person']);
		$cg2 = null;
		if (!empty($data['careGivers'][1]))
			$cg2 = $cgr->find($data['careGivers'][1]['person']);

		$name = $cg1->formatName(['preferredOnly' => true]);

		if ($cg2 instanceof Person)
		{
			$name2   = $cg2->formatName(['preferredOnly' => true]);
			$surname = substr($name, 0, strpos($name, ':') + 1);
			$name2   = trim(str_replace($surname, '', $name2));
			if (!empty($name2))
				$name .= ' & ' . $name2;
		}

		return $name;
	}

	/**
	 * @param $id
	 *
	 * @return CareGiver|null|object
	 */
	public function retrieveCareGiver($id)
	{
		if ($id > 0)
			return $this->objectManager->getRepository(CareGiver::class)->find($id);

		return null;
	}

	/**
	 * @param $details
	 *
	 * @return CareGiver|null|object
	 */
	public function findOneCareGiverByPerson($details)
	{
		$cg = $this->objectManager->getRepository(CareGiver::class)->findOneBy($details);
		if (!$cg instanceof CareGiver)
		{
			$cg = new CareGiver();
			if (!empty($details['person']))
				$cg->setPerson($this->objectManager->getRepository(Person::class)->find($details['person']));
			if (!empty($details['family']))
				$cg->setFamily($this->objectManager->getRepository(Family::class)->find($details['family']));
		}

		return $cg;
	}

	public function getStudentFromPerson(int $person): Student
	{
		$student = $this->getStudentRepository()->find($person);
		if (!$student instanceof Student)
			$student = new Student();

		return $student;
	}

	/**
	 * @return \Busybee\People\StudentBundle\Repository\StudentRepository|\Doctrine\ORM\EntityRepository
	 */
	public function getStudentRepository()
	{
		return $this->objectManager->getRepository(Student::class);
	}

	public function robjectManageroveEntity($entity)
	{
		$this->objectManager->robjectManagerove($entity);
	}
}