<?php

namespace Busybee\InstituteBundle\Model;

use Busybee\InstituteBundle\Entity\StudentYear;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;

class YearManager
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var Form
     */
    private $form;

    /**
     * @var array
     */
    private $data;

    /**
     * YearManager constructor.
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->manager;
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {


        $this->data = $event->getData();
        $this->form = $event->getForm();

        $year = $this->form->getData();

        if (isset($this->data['terms']) && is_array($this->data['terms'])) {
            foreach ($this->data['terms'] as $q => $w) {
                $w['year'] = $year->getId();
                $this->data['terms'][$q] = $w;
            }
        }

        if (isset($this->data['specialDays']) && is_array($this->data['specialDays'])) {
            foreach ($this->data['specialDays'] as $q => $w) {
                $w['year'] = $year->getId();
                $this->data['specialDays'][$q] = $w;
            }
        }

        if (isset($this->data['studentYears']) && is_array($this->data['studentYears'])) {
            foreach ($this->data['studentYears'] as $q => $w) {
                $w['year'] = $year->getId();
                $w['old_sequence'] = $w['sequence'];
                $this->data['studentYears'][$q] = $w;
            }
        }

        $this->turnYearSequenceOff();
        $this->manageStudentYears();

        $event->setData($this->data);

        return $event;

    }

    /**
     *
     */
    public function turnYearSequenceOff()
    {
        $rsm = new ResultSetMapping();
        $om = $this->manager->getClassMetadata(StudentYear::class);
        $this->executeQuery('ALTER TABLE `' . $om->table['name'] . '` DROP INDEX `sequence`', $rsm);
    }

    /**
     * @param $sql
     * @param $rsm
     */
    private function executeQuery($sql, $rsm)
    {

        $query = $this->manager->createNativeQuery($sql, $rsm);
        try {
            $query->execute();
        } catch (PDOException $e) {
            if (!in_array($e->getErrorCode(), []))
                dump($e);
        } catch (DriverException $e) {
            if (!in_array($e->getErrorCode(), ['1091']))
                dump($e);
        }

    }

    /**
     *
     */
    private function manageStudentYears()
    {
        if (!empty($this->data['studentYears']) && is_array($this->data['studentYears'])) {
            $studentYears = array();
            foreach ($this->data['studentYears'] as $w)
                if (!empty($w) && !empty($w['year'])) {
                    $studentYears[] = $w;
                }
            $year = $this->form->getData();

            $sYears = new ArrayCollection();

            $seq = 0;
            foreach ($studentYears as $q => $w) {
                $sy = $this->manager->getRepository(StudentYear::class)->findOneByYearSequence($w['year'], $w['old_sequence']);
                $sy->setSequence(++$seq);
                $studentYears[$q]['sequence'] = $seq;
                $sYears->add($sy);
                unset($studentYears[$q]['old_sequence']);
            }
            $year->setStudentYears($sYears);
            $this->form->setData($year);

            $this->data['studentYears'] = $studentYears;
        }
    }

    /**
     *
     */
    public function turnYearSequenceOn()
    {
        $rsm = new ResultSetMapping();
        $om = $this->manager->getClassMetadata(StudentYear::class);
        $this->executeQuery('ALTER TABLE `' . $om->table['name'] . '` ADD UNIQUE `sequence` (`year_id`, `sequence`)', $rsm);
    }
}