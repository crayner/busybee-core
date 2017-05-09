<?php

namespace Busybee\TimeTableBundle\Events;

use Busybee\SystemBundle\Setting\SettingManager;
use Busybee\TimeTableBundle\Entity\Day;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TimeTableSubscriber implements EventSubscriberInterface
{
    private $days;

    public function __construct(SettingManager $sm)
    {
        $this->days = $sm->get('schoolWeek');
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_submit
        // event and that the preSubmit method should be called.
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();

        if (count($this->days) != $data->getDays()->count() && count($this->days) > 0) {
            foreach ($this->days as $day) {
                $set = false;

                foreach ($data->getDays() as $d) {
                    if ($d->getName() == $day)
                        $set = true;
                }
                if (!$set) {
                    $td = new Day();
                    $td->setName($day);
                    $td->setDayType(true);
                    $data->addDay($td);
                }
            }

        }

        $event->setData($data);
    }
}