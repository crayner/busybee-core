<?php

namespace Busybee\PersonBundle\Form;

use Busybee\FormBundle\Type\YesNoType;
use Busybee\PersonBundle\Entity\Person;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToIntTransformer;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StaffType extends AbstractType
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
            ->add('person', HiddenType::class, array(
                    'attr'  =>  array(
                        'class' => 'staffMember',
                    )
                )
            )
            ->add('type', ChoiceType::class, array(
                    'label' => 'person.label.staff.type',
                    'choices' => $this->sm->get('Staff.Categories'),
                    'placeholder' => 'person.placeholder.staff.type',
                    'attr'  =>  array(
                        'class' => 'staffMember',
                    )
                )
            )
            ->add('jobTitle', null, array(
                    'label' => 'person.label.staff.jobTitle',
                    'attr'  =>  array(
                        'class' => 'staffMember',
                    )
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
            'data_class' => 'Busybee\PersonBundle\Entity\Staff',
            'translation_domain' => 'BusybeePersonBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'staff';
    }


}
