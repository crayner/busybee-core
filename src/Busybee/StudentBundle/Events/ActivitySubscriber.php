<?php

namespace Busybee\StudentBundle\Events;

use Busybee\StudentBundle\Entity\Activity;
use Busybee\StudentBundle\Entity\Student;
use Busybee\StudentBundle\Form\StudentActivityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ActivitySubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_submit
        // event and that the preSubmit method should be called.
        return array(
            FormEvents::POST_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        $entity = $form->getData();

        if (!empty($entity->getGrades()) && !empty($entity->getYear())) {
            $grades = $entity->getGrades();
            $year = $entity->getYear();
            $form->add('students', StudentActivityType::class,
                [
                    'class' => Student::class,
                    'choice_label' => 'formatName',
                    'query_builder' => function (EntityRepository $er) use ($grades, $year) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.grades', 'r')
                            ->leftJoin('r.grade', 'g')
                            ->leftJoin('s.person', 'p')
                            ->orderBy('p.surname', 'ASC')
                            ->addOrderBy('p.firstName', 'ASC')
                            ->setParameter('grades', $grades)
                            ->where('g.grade IN (:grades)')
                            ->leftJoin('g.year', 'y')
                            ->andWhere('y.id = :year_id')
                            ->setParameter('year_id', $year->getId());
                    },
                    'label' => 'activity.student.label.list',
                    'multiple' => true,
                    'expanded' => true,
                    'attr' => [
                        'help' => 'activity.student.help.list',
                    ],
                    'label_attr' => [
                        'class' => 'studentList',
                    ],
                ]
            );
            $form->add('possibleList', EntityType::class,
                [
                    'mapped' => false,
                    'class' => Activity::class,
                    'choice_label' => 'name',
                    'query_builder' => function (EntityRepository $er) use ($grades, $year) {
                        return $er->createQueryBuilder('a')
                            ->leftJoin('a.year', 'y')
                            ->orderBy('a.name', 'ASC')
                            ->where('y.id = :year_id')
                            ->andWhere('a.grades LIKE :grades')
                            ->setParameter('year_id', $year->getId())
                            ->setParameter('grades', '%' . implode('%', $grades) . '%');
                    },
                    'attr' => [
                        'help' => 'activity.student.help.possibleList',
                    ],
                    'placeholder' => 'activity.student.placeholder.possibleList',
                    'label' => 'activity.student.label.possibleList',
                    'required' => false,
                ]
            );
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
        $entity = $form->getData();
        if (!empty($data['grades']) && $entity->getGrades() != $data['grades'])
            $entity->setGrades($data['grades']);

        $form->setData($entity);
    }

}
