<?php

namespace Busybee\ActivityBundle\Form;

use Busybee\Core\CalendarBundle\Entity\Grade;
use Busybee\InstituteBundle\Entity\Space;
use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\StaffBundle\Entity\Staff;
use Busybee\ActivityBundle\Entity\Activity;
use Busybee\ActivityBundle\Events\ActivitySubscriber;
use Busybee\StudentBundle\Model\StudentManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var StudentManager
     */
    private $studentManager;

    /**
     * ActivityType constructor.
     * @param   ObjectManager $om
     */
    public function __construct(ObjectManager $om, RequestStack $request, StudentManager $studentManager)
    {
        $this->om = $om;
        $this->request = $request;
        $this->studentManager = $studentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $year = $options['year_data'];
        $this_id = $options['data']->getId();

        $builder
            ->add('name', null,
                [
                    'label' => 'activity.label.name',
                    'attr' => [
                        'class' => 'monitorChange',
                    ],
                ]
            )
            ->add('nameShort', null,
                [
                    'label' => 'activity.label.nameShort',
                    'attr' => [
                        'class' => 'monitorChange',
                    ],
                ]
            )
            ->add('year', HiddenType::class)
            ->add('grades', EntityType::class,
                [
                    'label' => 'activity.label.grades',
                    'class' => Grade::class,
                    'placeholder' => 'activity.placeholder.grades',
                    'multiple' => true,
                    'required' => false,
                    'attr' => [
                        'help' => 'activity.help.grades',
                        'class' => 'monitorChange',
                    ],
                    'choice_label' => 'grade',
                    'query_builder' => function (EntityRepository $er) use ($year) {
                        return $er->createQueryBuilder('g')
                            ->leftJoin('g.year', 'y')
                            ->where('y.id = :year_id')
                            ->setParameter('year_id', $year->getId())
                            ->orderBy('g.sequence', 'DESC');
                    },
                ]
            )
            ->add('teachingLoad', IntegerType::class,
                [
                    'label' => 'activity.teachingLoad.label',
                    'attr' => [
                        'class' => 'monitorChange',
                        'help' => 'activity.teachingLoad.help',
                    ],
                    'empty_data' => 0,

                ]
            )
            ->add('tutor1', EntityType::class,
                [
                    'label' => 'activity.label.tutor1',
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'placeholder' => 'activity.placeholder.tutor',
                    'attr' => [
                        'help' => 'activity.help.tutor',
                        'class' => 'monitorChange',
                    ],
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.person', 'p')
                            ->addOrderBy('p.surname', 'ASC')
                            ->addOrderBy('p.firstName', 'ASC');
                    },
                    'required' => false,
                ]
            )
            ->add('tutor2', EntityType::class,
                [
                    'label' => 'activity.label.tutor2',
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'placeholder' => 'activity.placeholder.tutor',
                    'attr' => [
                        'help' => 'activity.help.tutor',
                        'class' => 'monitorChange',
                    ],
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.person', 'p')
                            ->addOrderBy('p.surname', 'ASC')
                            ->addOrderBy('p.firstName', 'ASC');
                    },
                    'required' => false,
                ]
            )
            ->add('tutor3', EntityType::class,
                [
                    'label' => 'activity.label.tutor3',
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'placeholder' => 'activity.placeholder.tutor',
                    'attr' => [
                        'class' => 'monitorChange',
                        'help' => 'activity.help.tutor'
                    ],
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.person', 'p')
                            ->addOrderBy('p.surname', 'ASC')
                            ->addOrderBy('p.firstName', 'ASC');
                    },
                    'required' => false,
                ]
            )
            ->add('space', EntityType::class,
                [
                    'label' => 'activity.space.label',
                    'class' => Space::class,
                    'choice_label' => 'nameCapacity',
                    'placeholder' => 'activity.space.placeholder',
                    'attr' => [
                        'class' => 'monitorChange',
                        'help' => 'activity.space.help'
                    ],
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->addOrderBy('s.name', 'ASC');
                    },
                    'required' => false,
                ]
            )
            ->add('changeRecord', EntityType::class,
                [
                    'label' => false,
                    'attr' => array(
                        'class' => 'formChanged changeRecord',
                    ),
                    'class' => Activity::class,
                    'choice_label' => 'nameYear',
                    'mapped' => false,
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) use ($year) {
                        return $er->createQueryBuilder('a')
                            ->leftJoin('a.year', 'y')
                            ->addOrderBy('a.name', 'ASC')
                            ->addOrderBy('a.nameShort', 'ASC')
                            ->where('y.id = :year_id')
                            ->setParameter('year_id', $year);
                    },
                    'placeholder' => 'activity.placeholder.changeRecord',
                ]
            )
            ->add('studentReference', EntityType::class,
                [
                    'label' => 'activity.studentReference.label',
                    'attr' => array(
                        'class' => 'monitorChange',
                        'help' => 'activity.studentReference.help',
                    ),
                    'class' => Activity::class,
                    'choice_label' => 'nameYear',
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) use ($year, $this_id) {
                        $r = $er->createQueryBuilder('a')
                            ->leftJoin('a.year', 'y')
                            ->addOrderBy('a.name', 'ASC')
                            ->addOrderBy('a.nameShort', 'ASC')
                            ->where('y.id = :year_id')
                            ->setParameter('year_id', $year);

                        if (!is_null($this_id))
                            $r->andWhere('a.id != :this_id')
                            ->setParameter('this_id', $this_id);
                        return $r;
                    },
                    'placeholder' => 'activity.studentReference.placeholder',
                ]
            );

        $builder->get('year')->addModelTransformer(new EntityToStringTransformer($this->om, Year::class));

        $builder->addEventSubscriber(new ActivitySubscriber($this->request, $this->studentManager));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Activity::class,
                'translation_domain' => 'BusybeeStudentBundle',
            ]
        );
        $resolver->setRequired(
            [
                'year_data',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'activity';
    }


    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['year_data'] = $options['year_data'];
    }
}
