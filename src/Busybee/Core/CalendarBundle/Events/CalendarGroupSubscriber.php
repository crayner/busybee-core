<?php

namespace Busybee\Core\CalendarBundle\Events;

use Busybee\Core\CalendarBundle\Model\CalendarGroupManager;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CalendarGroupSubscriber implements EventSubscriberInterface
{
	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * @var CalendarGroupManager
	 */
	private $gradeManager;

	/**
	 * DepartmentType constructor.
	 *
	 * @param SettingManager $om
	 */
	public function __construct(SettingManager $settingManager, CalendarGroupManager $gradeManager)
	{
		$this->settingManager = $settingManager;
		$this->gradeManager   = $gradeManager;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		// Tells the dispatcher that you want to listen on the form.pre_set_data
		// event and that the preSetData method should be called.
		return array(
			FormEvents::PRE_SUBMIT   => 'preSubmit',
			FormEvents::PRE_SET_DATA => 'preSetData',
		);
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSetData(FormEvent $event)
	{
		$form = $event->getForm();

		$data = $event->getData();
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{
		$data = $event->getData();

		if (isset($data['nameShort']))
		{
			$groups       = $this->settingManager->get('student.groups._flip');
			$data['name'] = $groups[$data['nameShort']];
		}

		$event->setData($data);
	}
}