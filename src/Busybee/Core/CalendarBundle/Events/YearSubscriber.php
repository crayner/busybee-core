<?php
namespace Busybee\Core\CalendarBundle\Events;

use Busybee\Core\CalendarBundle\Entity\CalendarGroup;
use Busybee\Core\CalendarBundle\Model\YearManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class YearSubscriber implements EventSubscriberInterface
{
	/**
	 * @var YearManager
	 */
	private $yearManager;

	/**
	 * YearSubscriber constructor.
	 *
	 * @param YearManager $ym
	 */
	public function __construct(YearManager $ym)
	{
		$this->yearManager = $ym;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return [
			FormEvents::PRE_SUBMIT => 'preSubmit',
		];
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{
		$data   = $event->getData();
		$form   = $event->getForm();
		$entity = $form->getData();


		if (!empty($data['calendarGroups']))
		{
			$cg  = $entity->getCalendarGroups();
			$new = new ArrayCollection();
			$seq = 0;

			if ($cg->first()->getSequence() < 100)
				$seq = 100;

			foreach ($data['calendarGroups'] as $q => $w)
			{
				$w['sequence'] = ++$seq;

				$data['calendarGroups'][$q] = $w;

				$exists = new CalendarGroup();

				foreach ($cg as $cal)
					if ($cal->getNameShort() === $w['nameShort'])
					{
						$exists = $cal;
						$cg->removeElement($exists);
						break;
					}

				$exists->setSequence($seq);

				if (empty($exists->getId()))
				{
					$exists->setName($w['name']);
					$exists->setNameShort($w['nameShort']);
					$exists->setYear($entity);
				}
				$new->add($exists);
			}
			$entity->setCalendarGroups($new);
		}

		if ($cg->count() > 0)
		{
			foreach ($cg->toArray() as $item)
				$this->yearManager->getObjectManager()->remove($item);

			$this->yearManager->getObjectManager()->flush();
		}

		$specDays = [];
		if (isset($data['specialDays']) && !empty($entity->getSpecialDays()) && $entity->getSpecialDays()->count() > 0)
		{
			foreach ($entity->getSpecialDays() as $key => $sd)
			{
				$delete = true;
				foreach ($data['specialDays'] as $q => $nsd)
				{
					$day = $nsd['day'];
					if ($sd->getDay()->format('Ymd') == $day['year'] . str_pad($day['month'], 2, '0', STR_PAD_LEFT) . str_pad($day['day'], 2, '0', STR_PAD_LEFT))
					{
						$delete         = false;
						$specDays[$key] = $nsd;
						unset($data['specialDays'][$q]);
						break;
					}
					if ($delete)
					{
						$entity->getSpecialDays()->remove($key);
						$this->yearManager->getObjectManager()->remove($sd);
						$this->yearManager->getObjectManager()->flush();
					}
				}
			}
		}
		if (!empty($data['specialDays']))
			$specDays = array_merge($specDays, $data['specialDays']);

		if (!empty($specDays))
			$data['specialDays'] = $specDays;

		if (!empty($entity->getDownloadCache()) && file_exists($entity->getDownloadCache()))
			unlink($entity->getDownloadCache());

		$event->setData($data);
		$form->setData($entity);
	}
}