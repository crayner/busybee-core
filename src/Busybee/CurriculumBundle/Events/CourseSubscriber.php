<?php

namespace Busybee\CurriculumBundle\Events;

use Busybee\Core\SystemBundle\Setting\SettingManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CourseSubscriber implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $studentGroups;

    /**
     * CourseSubscriber constructor.
     * @param array $studentGroups
     */
    public function __construct($studentGroups)
    {
        $this->studentGroups = $studentGroups;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_submit
        // event and that the preSubmit method should be called.
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit'
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        $form = $event->getForm();
        $course = $form->getData();

        $sg = $data['targetYear'];
        $ty = array();
        foreach ($this->studentGroups as $value)
            if (in_array($value, $sg))
                $ty[] = $value;
        $data['targetYear'] = $ty;
        $course->setTargetYear($ty);

        $form->setData($course);
        $event->setData($data);
    }
}