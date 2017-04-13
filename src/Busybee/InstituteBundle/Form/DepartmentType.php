<?php

namespace Busybee\InstituteBundle\Form;

use Busybee\FormBundle\Type\SettingChoiceType;
use Busybee\InstituteBundle\Entity\Department;
use Busybee\InstituteBundle\Events\DepartmentSubscriber;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartmentType extends AbstractType
{
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
            ->add('staff', CollectionType::class,
                [
                    'entry_type' => DepartmentStaffType::class,
                    'attr' =>
                        [
                            'class' => 'staffList',
                        ],
                    'allow_add' => true,
                    'allow_delete' => true,
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
