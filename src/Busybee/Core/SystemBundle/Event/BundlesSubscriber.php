<?php

namespace Busybee\Core\SystemBundle\Event;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class BundlesSubscriber implements EventSubscriberInterface
{

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
		$data     = $event->getData();
		$form     = $event->getForm();
		$formData = $form->getData();


		// Do any sort stuff here ...
		$w = new ArrayCollection();
		foreach ($data['bundles'] as $name => $bundle)
			$w->add($formData->getBundles()->get($name));

		$formData->setBundles($w);
		dump($data);
		dump($formData);
		$event->setData($data);
		$form->setData($formData);
	}
}