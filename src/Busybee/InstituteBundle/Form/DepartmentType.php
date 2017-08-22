<?php

namespace Busybee\InstituteBundle\Form;

use Busybee\CurriculumBundle\Entity\Course;
use Busybee\Core\FormBundle\Type\SettingChoiceType;
use Busybee\InstituteBundle\Entity\Department;
use Busybee\InstituteBundle\Events\DepartmentSubscriber;
use Busybee\SystemBundle\Setting\SettingManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartmentType extends AbstractType
{
    /**
     * @var SettingManager
     */
    private $sm;

    /**
     * DepartmentType constructor.
     * @param SettingManager $om
     */
    public function __construct(SettingManager $sm)
    {
        $this->sm = $sm;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null,
                [
                    'label' => 'department.label.name'
                ]
            )
            ->add('type', SettingChoiceType::class,
                [
                    'label' => 'department.label.type',
                    'setting_name' => 'department.type.list',
                    'placeholder' => 'department.placeholder.type',
                ]
            )
            ->add('nameShort', null,
                [
                    'label' => 'department.label.nameShort'
                ]
            )
            ->add('courses', CollectionType::class,
                [
                    'entry_type' => EntityType::class,
                    'attr' =>
                        [
                            'class' => 'courseList',
                            'help' => 'department.help.courses',
                        ],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_options' => [
                        'label' => 'department.course.label.name',
                        'class' => Course::class,
                        'choice_label' => 'name',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('c')
                                ->orderBy('c.name', 'ASC');
                        },
                        'placeholder' => 'department.course.placeholder.name',
                    ],
                ]
            )
            ->add('departmentList', EntityType::class, array(
                    'class' => Department::class,
                    'attr' => array(
                        'class' => 'departmentList changeRecord formChanged',
                    ),
                    'label' => '',
                    'mapped' => false,
                    'choice_label' => 'name',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('d')
                            ->orderBy('d.name', 'ASC');
                    },
                    'placeholder' => 'department.placeholder.departments',
                    'required' => false,
                    'data' => $options['data']->getId(),
                )
            );

        $builder->addEventSubscriber(new DepartmentSubscriber($this->sm));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Department::class,
            'translation_domain' => 'BusybeeInstituteBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'department';
    }


}
