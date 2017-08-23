<?php

namespace Busybee\TimeTableBundle\Events;

use Busybee\Core\SystemBundle\Setting\SettingManager;
use Busybee\TimeTableBundle\Entity\Column;
use Busybee\TimeTableBundle\Entity\Period;
use Busybee\TimeTableBundle\Form\ColumnPeriodType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ColumnSubscriber implements EventSubscriberInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var array
     */
    private $days;

    /**
     * @var integer
     */
    private $tt_id;

    /**
     * ColumnSubscriber constructor.
     * @param ObjectManager $om
     * @param SettingManager $sm
     * @param $tt_id
     */
    public function __construct(ObjectManager $om, SettingManager $sm, $tt_id)
    {
        $this->om = $om;
        $this->days = $sm->get('SchoolDay.Periods');
        $this->tt_id = $tt_id;
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

        if ($data->getColumns()->count() > 0) {
            foreach ($data->getColumns() as $column) {
                if ($column->getPeriods()->count() == 0) {
                    foreach ($this->days as $name => $val) {
                        $period = new Period();
                        $period->setName($name);
                        $period->setNameShort($val['abbr']);
                        $period->setStart(new \DateTime($val['start']));
                        $period->setEnd(new \DateTime($val['end']));
                        $period->setColumn($column);
                        $this->om->persist($period);
                        $this->om->flush();
                        $column->addPeriod($period);
                    }
                }

            }
        }

        $event->setData($data);
    }
}