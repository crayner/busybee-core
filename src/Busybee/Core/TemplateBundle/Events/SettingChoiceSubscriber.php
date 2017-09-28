<?php

namespace Busybee\Core\TemplateBundle\Events;

use Busybee\Core\SystemBundle\Setting\SettingManager;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SettingChoiceSubscriber implements EventSubscriberInterface
{
	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * SettingSubscriber constructor.
	 *
	 * @param   SettingManager $settingManager
	 *
	 * @return  SettingChoiceSubscriber
	 */
	public function __construct(SettingManager $settingManager)
	{
		$this->settingManager = $settingManager;

		return $this;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		// Tells the dispatcher that you want to listen on the form.pre_set_data
		// event and that the preSetData method should be called.
		return array(
			FormEvents::PRE_SET_DATA => 'preSetData',
		);
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSetData(FormEvent $event)
	{
		$form = $event->getForm();

		$options = $form->getConfig()->getOptions();
		$name    = $form->getName();
		if (!$this->settingManager->settingExists($options['setting_name']))
		{
			$names = $this->settingManager->getLikeSettingNames($options['setting_name']);
			throw new \InvalidArgumentException('Setting ' . $options['setting_name'] . ' not found.' . $names);
		}
		$choices = $this->settingManager->get($options['setting_name']);
		dump($choices);
		if ($options['use_label_as_value'])
		{
			$x = [];
			foreach ($choices as $label)
				$x[$label] = $label;
			$choices = $x;
		}

		$newOptions                              = array();
		$newOptions['choices']                   = $choices;
		$newOptions['label']                     = isset($options['label']) ? $options['label'] : null;
		$newOptions['attr']                      = isset($options['attr']) ? $options['attr'] : [];
		$newOptions['translation_domain']        = isset($options['translation_domain']) ? $options['translation_domain'] : null;
		$newOptions['placeholder']               = isset($options['placeholder']) ? $options['placeholder'] : null;
		$newOptions['required']                  = isset($options['required']) ? $options['required'] : false;
		$newOptions['multiple']                  = isset($options['multiple']) ? $options['multiple'] : false;
		$newOptions['expanded']                  = isset($options['expanded']) ? $options['expanded'] : false;
		$newOptions['mapped']                    = isset($options['mapped']) ? $options['mapped'] : true;
		$newOptions['data']                      = isset($options['data']) ? $options['data'] : null;
		$newOptions['choice_translation_domain'] = isset($options['choice_translation_domain']) ? $options['choice_translation_domain'] : $newOptions['translation_domain'];

		//  Now replace the existing setting form element with a straight Choice
		$form->getParent()->add($name, ChoiceType::class, $newOptions);
	}
}