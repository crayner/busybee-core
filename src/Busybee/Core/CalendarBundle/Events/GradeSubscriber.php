<?php

namespace Busybee\Core\CalendarBundle\Events;

use Busybee\Core\SystemBundle\Setting\SettingManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GradeSubscriber implements EventSubscriberInterface
{
	/**
	 * @var SettingManager
	 */
	private $sm;

	/**
	 * DepartmentType constructor.
	 *
	 * @param SettingManager $om
	 */
	public function __construct(SettingManager $sm)
	{
		$this->sm = $sm;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		// Tells the dispatcher that you want to listen on the form.pre_set_data
		// event and that the preSetData method should be called.
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

		if (isset($data['grade']))
		{
			$groups       = $this->sm->get('student.groups._flip');
			$data['name'] = $groups[$data['grade']];
		}

		$event->setData($data);
	}
}