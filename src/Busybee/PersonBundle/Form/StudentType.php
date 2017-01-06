<?php

namespace Busybee\PersonBundle\Form;

use Busybee\PersonBundle\Entity\Person;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
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
        ;
        $builder->get('person')->addModelTransformer(new EntityToStringTransformer($this->manager, Person::class));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Busybee\PersonBundle\Entity\Student',
            'translation_domain' => 'BusybeePersonBundle',
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
