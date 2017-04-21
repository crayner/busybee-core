<?php

namespace Busybee\StudentBundle\Events;

use Busybee\StudentBundle\Entity\Student;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
            FormEvents::POST_SET_DATA => 'preSetData'
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

        if (!empty($entity->getGrades())) {
            $grades = implode(',', $entity->getGrades());
            $form->add('students', CollectionType::class, [
                    'label' => 'activity.label.students',
                    'entry_type' => EntityType::class,
                    'entry_options' => [
                        'choice_label' => 'formatName',
                        'multiple' => true,
                        'class' => Student::class,
                        'query_builder' => function (EntityRepository $er) use ($grades) {
                            return $er->createQueryBuilder('s')
                                ->where('e.grade IN (:grades)')
                                ->setParameter('grades', $grades)
                                ->orderBy('s.surname', 'ASC')
                                ->addOrderBy('s.firstName', 'ASC');
                        },
                    ],
                ]
            );
        }
        dump($form);
        $event->setData($data);
    }
}