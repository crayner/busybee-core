<?php

namespace Busybee\StudentBundle\Form;

use Busybee\FormBundle\Type\SettingChoiceType;
use Busybee\InstituteBundle\Entity\Year;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\StaffBundle\Entity\Staff;
use Busybee\StudentBundle\Entity\Activity;
use Busybee\StudentBundle\Entity\Student;
use Busybee\StudentBundle\Events\ActivitySubscriber;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * ActivityType constructor.
     * @param   ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
            ->add('year', EntityType::class,
                [
                    'label' => 'activity.label.year',
                    'class' => Year::class,
                    'choice_label' => 'name',
                    'placeholder' => 'activity.placeholder.year',
                    'required' => true,
                    'attr' => [
                        'class' => 'monitorChange',
                    ],
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('y')
                            ->orderBy('y.firstDay', 'DESC');
                    },
                ]
            )
            ->add('grades', SettingChoiceType::class,
                [
                    'label' => 'activity.label.grades',
                    'placeholder' => 'activity.placeholder.grades',
                    'setting_name' => 'student.groups',
                    'multiple' => true,
                    'attr' => [
                        'help' => 'activity.help.grades',
                        'class' => 'monitorChange',
                    ],
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
            ->add('changeRecord', EntityType::class,
                array(
                    'label' => false,
                    'attr' => array(
                        'class' => 'formChanged changeRecord',
                    ),
                    'class' => Activity::class,
                    'choice_label' => 'nameYear',
                    'mapped' => false,
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('a')
                            ->leftJoin('a.year', 'y')
                            ->addOrderBy('y.firstDay', 'DESC')
                            ->addOrderBy('a.name', 'ASC')
                            ->addOrderBy('a.nameShort', 'ASC');
                    },
                    'placeholder' => 'activity.placeholder.changeRecord',
                )
            );

        $builder->get('year')->addModelTransformer(new EntityToStringTransformer($this->om, Year::class));

        $builder->addEventSubscriber(new ActivitySubscriber());
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
