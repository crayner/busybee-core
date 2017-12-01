<?php

namespace Busybee\Core\CalendarBundle\Events;

use Busybee\Core\CalendarBundle\Model\YearManager;
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
			$seq = 0;
			foreach ($data['calendarGroups'] as $q => $w)
			{
				$w['sequence'] = ++$seq;

				$data['calendarGroups'][$q] = $w;
			}
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