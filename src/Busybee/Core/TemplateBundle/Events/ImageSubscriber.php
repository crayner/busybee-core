<?php

namespace Busybee\Core\TemplateBundle\Events;

use Busybee\Core\SystemBundle\Setting\SettingManager;
use Busybee\Core\TemplateBundle\Type\ChoiceSettingType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ImageSubscriber implements EventSubscriberInterface
{
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
		$form = $event->getForm();
		$data = $event->getData();

		if (!empty($form->getData()) && empty($data))
			$data = $form->getData();

		$event->setData($data);

	}
}