<?php

namespace Busybee\FormBundle\Events;

use Busybee\SystemBundle\Setting\SettingManager;
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
     * @param SettingManager $settingManager
     * @return SettingSubscriber
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
//        dump($form->get($form->getName())->getConfig()->getOptions());
        $options = $form->getConfig()->getOptions();
        $name = $form->getName();
        $choices = $this->settingManager->get($options['settingName']);

        $newOptions = array();
        $newOptions['choices'] = $choices;
        if (isset($options['label']))
            $newOptions['label'] = $options['label'];
        if (isset($options['attr']))
            $newOptions['attr'] = $options['attr'];
        if (isset($options['required']))
            $newOptions['required'] = $options['required'];
        if (isset($options['translation_domain']))
            $newOptions['translation_domain'] = $options['translation_domain'];

        //  Now replace the existing setting form element with a straight Choice
        $form->getParent()->add($name, ChoiceType::class, $newOptions);
    }
}