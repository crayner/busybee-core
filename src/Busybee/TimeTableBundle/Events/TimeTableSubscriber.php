<?php

namespace Busybee\TimeTableBundle\Events;

use Busybee\SystemBundle\Setting\SettingManager;
use Busybee\TimeTableBundle\Entity\Column;
use Busybee\TimeTableBundle\Entity\Day;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TimeTableSubscriber implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $days;

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * TimeTableSubscriber constructor.
     * @param SettingManager $sm
     * @param ObjectManager $om
     */
    public function __construct(SettingManager $sm, ObjectManager $om)
    {
        $this->days = $sm->get('schoolWeek');
        $this->om = $om;
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
            FormEvents::PRE_SUBMIT => 'preSubmit',
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

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $offset = 500;

        if (!empty($data['columns'])) {
            $cols = new ArrayCollection();
            $c = 1;
            foreach ($data['columns'] as $q => $w) {
                $column = $this->om->getRepository(Column::class)->find($w['id']);
                if (intval($w['sequence']) !== $c && intval($w['sequence']) <= 500)
                    $w['sequence'] = $c + $offset;
                else
                    $w['sequence'] = $c;

                if ($column instanceof Column) {
                    $column->setSequence($c);
                    $cols->add($column);
                }
                $data['columns'][$q] = $w;
                $c++;
            }
            $form->get('columns')->setData($cols);
        }
        $event->setData($data);
    }
}