<?php

namespace Busybee\People\StudentBundle\Events;

use Busybee\Core\CalendarBundle\Entity\CalendarGroup;
use Busybee\People\StudentBundle\Entity\StudentCalendarGroup;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StudentSubscriber implements EventSubscriberInterface
{
	/**
	 * @var ObjectManager
	 */
	private $om;

	/**
	 * StudentSubscriber constructor.
	 *
	 * @param ObjectManager $om
	 */
	public function __construct(ObjectManager $om)
	{
		$this->om = $om;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		// Tells the dispatcher that you want to listen on the form.pre_submit
		// event and that the preSubmit method should be called.
		return array(
			FormEvents::PRE_SUBMIT  => 'preSubmit',
			FormEvents::POST_SUBMIT => 'postSubmit',
		);
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{
		$data   = $event->getData();
		$form   = $event->getForm();
		$entity = $form->getData();

		if (is_null($entity)) return;

		if (is_null($data['citizenship1PassportScan'])) $data['citizenship1PassportScan'] = $entity->getCitizenship1PassportScan();
		if (is_null($data['nationalIDCardScan'])) $data['nationalIDCardScan'] = $entity->getNationalIDCardScan();

		if (!empty($data['grades']))
		{
			foreach ($data['grades'] as $q => $w)
			{
				$w['student']       = strval($entity->getId());
				$data['grades'][$q] = $w;
				if ($entity->getGrades()->containsKey($q) && is_null($entity->getCalendarGroups()->get($q)->getNameShort()))
				{
					$grade = $entity->getCalendarGroups()->get($q);
					$grade->setCalendarGroup($this->om->getRepository(CalendarGroup::class)->find($w['nameShort']));
				}
			}
		}

		$event->setData($data);
	}

	/**
	 * @param FormEvent $event
	 */
	public function postSubmit(FormEvent $event)
	{
		$data   = $event->getData();
		$form   = $event->getForm();
		$entity = $form->getData();

		$results = $this->om
			->getRepository(StudentCalendarGroup::class)
			->createQueryBuilder('g')
			->leftJoin('g.student', 's')
			->where('s.id = :stu_id')
			->setParameter('stu_id', $entity->getId())
			->getQuery()
			->getResult();

		if (!empty($results))
		{
			$existing = $entity->getCalendarGroups();
			foreach ($results as $grade)
				if (!$existing->contains($grade))
					$this->om->remove($grade);
			$this->om->flush();
		}

		$event->setData($data);
	}
}