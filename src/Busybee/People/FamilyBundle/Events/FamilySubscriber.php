<?php

namespace Busybee\People\FamilyBundle\Events;

use Busybee\People\FamilyBundle\Entity\CareGiver;
use Busybee\People\FamilyBundle\Model\FamilyManager;
use Busybee\People\PersonBundle\Entity\Person;
use Busybee\People\StaffBundle\Entity\Staff;
use Busybee\People\StudentBundle\Entity\Student;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FamilySubscriber implements EventSubscriberInterface
{
	/**
	 * @var FamilyManager
	 */
	private $familyManager;

	/**
	 * FamilySubscriber constructor.
	 *
	 * @param FamilyManager $pm
	 */
	public function __construct(FamilyManager $familyManager)
	{
		$this->familyManager = $familyManager;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		// Tells the dispatcher that you want to listen on the form.pre_submit
		// event and that the preSubmit method should be called.
		return array(
			FormEvents::PRE_SUBMIT => 'preSubmit',

		);
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{
		$data = $event->getData();

		$form = $event->getForm();

		unset($data['address1_list'], $data['address2_list']);


		if (empty($data['name']))
			$data['name'] = $this->familyManager->generateFamilyName($data);

		$students = array();
		if (isset($data['students']) && is_array($data['students']))
			foreach ($data['students'] as $key => $id)
				if (in_array($id, $students))
				{
					unset($data['students'][$key]);
				}
				else
				{
					$students[] = $id;
				}

		// Address Management
		unset($data['address1_list'], $data['address2_list']);
		if (!empty($data['address1']) || !empty($data['address2']))
		{
			if ($data['address1'] == $data['address2'])
				$data['address2'] = "";
			elseif (empty($data['address1']) && !empty($data['address2']))
			{
				$data['address1'] = $data['address2'];
				$data['address2'] = "";
			}
		}

		$family = $form->getData();

		$careGivers = [];
		if (!empty($data['careGivers']) && is_array($data['careGivers']))
		{
			foreach ($data['careGivers'] as $q => $w)
				if (!empty($w) && !empty($w['person']))
				{
					$w['contactPriority'] = $q + 1;
					$careGivers[]         = $w;
				}
		}
		$data['careGivers'] = $careGivers;

		if (empty($data['careGivers']) || empty($data['careGivers'][0]['person']))
			unset($data['careGivers']);
		$careGivers = new ArrayCollection();

		if (is_array($data['careGivers']))
		{
			foreach ($data['careGivers'] as $q => $w)
			{
				$data['careGivers'][$q]['contactPriority'] = $q + 1;
			}

			if ($family->getId() > 0)
			{
				foreach ($data['careGivers'] as $q => $w)
				{
					$cg                               = $this->familyManager->findOneCareGiverByPerson(array('person' => $w['person'], 'family' => $family->getId()));
					$data['careGivers'][$q]['cg']     = $cg;
					$data['careGivers'][$q]['family'] = $family->getId();
				}

				usort($data['careGivers'], function ($item1, $item2) {
					return $item1['currentOrder'] <=> $item2['currentOrder'];
				});
			}

			foreach ($data['careGivers'] as $q => $w)
			{
				if (!empty($data['careGivers'][$q]['cg']))
					$careGivers->add($data['careGivers'][$q]['cg']);
				unset($data['careGivers'][$q]['cg'], $data['careGivers'][$q]['currentOrder']);
			}
		}

		$a  = $family->getCareGivers()->toArray();
		$b  = $careGivers->toArray();
		$xx = array_diff($a, $b);

		foreach ($xx as $cg)
			$this->familyManager->removeEntity($cg);
		$family->setCareGivers($careGivers);

		$students = new ArrayCollection();
		if (!empty($data['students']) && is_array($data['students']))
		{
			foreach ($data['students'] as $q => $w)
				if (!empty($w) && !empty($w['person']))
				{
					$student = $this->familyManager->getStudentFromPerson($w['person']);
					if (empty($student->getHouse()))
						$student->setHouse($data['house']);
					if (empty($student->getFirstLanguage()))
						$student->setFirstLanguage($data['firstLanguage']);
					if (empty($student->getSecondLanguage()))
						$student->setSecondLanguage($data['secondLanguage']);
					$students->add($student);
				}
		}


		$family->setStudents($students);

		$form->setData($family);

		$event->setData($data);
	}
}