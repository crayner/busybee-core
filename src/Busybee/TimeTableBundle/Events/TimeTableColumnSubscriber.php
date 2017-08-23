<?php

namespace Busybee\TimeTableBundle\Events;

use Busybee\Core\SystemBundle\Setting\SettingManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TimeTableColumnSubscriber implements EventSubscriberInterface
{
    /**
     * @var SettingManager
     */
    private $sm;

    /**
     * ColumnSubscriber constructor.
     * @param SettingManager $sm
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

        if (!is_null($data)) {
            if ($data->getStart()->format('H:i') === '00:00')
                $data->setStart(new \DateTime($this->sm->get('SchoolDay.Begin')));

            if ($data->getEnd()->format('H:i') === '00:00')
                $data->setEnd(new \DateTime($this->sm->get('SchoolDay.Finish')));
        }

        $event->setData($data);
    }
}