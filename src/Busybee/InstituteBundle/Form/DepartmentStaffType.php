<?php

namespace Busybee\InstituteBundle\Form;

use Busybee\Core\FormBundle\Type\SettingChoiceType;
use Busybee\InstituteBundle\Entity\Department;
use Busybee\InstituteBundle\Entity\DepartmentStaff;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\People\StaffBundle\Entity\Staff;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartmentStaffType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * DepartmentStaffType constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, SettingManager $sm)
    {
        $this->om = $om;
        $this->sm = $sm;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['staff_type'] = $options['staff_type'] === 'Learning Area' ? 'Learning' : 'Administration';
        $builder
            ->add('staff', EntityType::class,
                [
                    'label' => 'department.staff.label.member',
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.person', 'p')
                            ->orderBy('p.surname', 'ASC')
                            ->addOrderBy('p.firstName', 'ASC');
                    },
                    'placeholder' => 'department.staff.placeholder.member',
                ]
            )
            ->add('staffType', SettingChoiceType::class,
                [
                    'label' => 'department.staff.label.type',
                    'setting_name' => 'department.staff.type.list.' . $options['staff_type'],
                    'placeholder' => 'department.staff.placeholder.type',
                ]
            )
            ->add('department', HiddenType::class);

        $builder->get('department')->addModelTransformer(new EntityToStringTransformer($this->om, Department::class));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DepartmentStaff::class,
            'translation_domain' => 'BusybeeInstituteBundle',
        ));
        $resolver->setRequired(
            [
                'staff_type',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'departmentStaff';
    }
}
