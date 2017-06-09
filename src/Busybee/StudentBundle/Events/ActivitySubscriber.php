<?php

namespace Busybee\StudentBundle\Events;

use Busybee\StudentBundle\Entity\Activity;
use Busybee\StudentBundle\Entity\Student;
use Busybee\StudentBundle\Form\StudentActivityType;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ActivitySubscriber implements EventSubscriberInterface
{
    /**
     * @var RequestStack
     */
    private $request;

    /**
     * ActivitySubscriber constructor.
     * @param RequestStack $request
     */
    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_submit
        // event and that the preSubmit method should be called.
        return [
            FormEvents::POST_SET_DATA => 'preSetData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        $entity = $form->getData();

        if ($entity->getGrades()->count() > 0) {
            $grades = $entity->getGrades();
            $gstring = [];
            foreach ($grades as $grade)
                $gstring[] = strval($grade->getId());
            $year = $entity->getYear();

            if (!$entity->getStudentReference() instanceof Activity)
                $form->add('students', StudentActivityType::class,
                    [
                        'class' => Student::class,
                        'choice_label' => 'formatName',
                        'query_builder' => function (EntityRepository $er) use ($gstring, $year) {
                            return $er->createQueryBuilder('s')
                                ->leftJoin('s.grades', 'i')
                                ->leftJoin('i.grade', 'g')
                                ->leftJoin('s.person', 'p')
                                ->where('g.id IN (:grades)')
                                ->setParameter('grades', $gstring, Connection::PARAM_STR_ARRAY)
                                ->andWhere('i.status IN (:status)')
                                ->setParameter('status', ['Future', 'Current'], Connection::PARAM_STR_ARRAY)
                                ->orderBy('p.surname')
                                ->addOrderBy('p.firstName');
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
        }

        $activity = $this->request->getCurrentRequest()->get('activity');

        if (($entity->getStudentReference() instanceof Activity || (!is_null($activity) && $activity['studentReference'] > 0)) && $form->has('students'))
            $form->remove('students');

        $event->setData($data);
    }
}
