<?php

namespace Busybee\StudentBundle\Form;

use Busybee\FormBundle\Type\SettingChoiceType;
use Busybee\PersonBundle\Entity\Person;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\StudentBundle\Entity\Student;
use Busybee\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $manager ;

    /**
     * @var SettingManager
     */
    private $sm ;

    /**
     * StaffType constructor.
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager, SettingManager $sm)
    {
        $this->manager = $manager ;
        $this->sm = $sm ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('person', HiddenType::class,
                array(
                    'attr'  =>  array(
                        'class' => 'student',
                    )
                )
            )
            ->add('startAtSchool', DateType::class, array(
                    'years' => range(date('Y', strtotime('-25 years')), date('Y', strtotime('+1 year'))),
                    'label' => 'student.label.startAtSchool',
                    'attr' => array(
                        'help' => 'student.help.startAtSchool',
                        'class' => 'student',
                    ),
                )
            )
            ->add('startAtThisSchool', DateType::class, array(
                    'years' => range(date('Y', strtotime('-25 years')), date('Y', strtotime('+1 year'))),
                    'label' => 'student.label.startAtThisSchool',
                    'attr' => array(
                        'help' => 'student.help.startAtThisSchool',
                        'class' => 'student',
                    ),
                )
            )
            ->add('lastAtThisSchool', DateType::class, array(
                    'years' => range(date('Y', strtotime('-5 years')), date('Y', strtotime('+18 months'))),
                    'label' => 'student.label.lastAtThisSchool',
                    'attr' => array(
                        'help' => 'student.help.lastAtThisSchool',
                        'class' => 'student',
                    ),
                    'required' => false,
                )
            )
            ->add('status', SettingChoiceType::class, array(
                    'label' => 'student.label.status',
                    'settingName' => 'Student.Status.List',
                    'attr' => array(
                        'help' => 'student.help.status',
                        'class' => 'student',
                    ),
                    'required' => true,
                )
            );
        $builder->get('person')->addModelTransformer(new EntityToStringTransformer($this->manager, Person::class));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Student::class,
            'translation_domain' => 'BusybeeStudentBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'student';
    }


}
