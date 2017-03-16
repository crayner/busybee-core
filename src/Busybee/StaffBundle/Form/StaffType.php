<?php

namespace Busybee\StaffBundle\Form;

use Busybee\FormBundle\Type\SettingType;
use Busybee\PersonBundle\Entity\Person;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToIntTransformer;
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
    private $manager;

    /**
     * @var SettingManager
     */
    private $sm;

    /**
     * StaffType constructor.
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager, SettingManager $sm)
    {
        $this->manager = $manager;
        $this->sm = $sm;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('person', HiddenType::class, array(
                    'attr' => array(
                        'class' => 'staffMember',
                    )
                )
            )
            ->add('staffType', SettingType::class, array(
                    'label' => 'staff.label.stafftype',
                    'setting_name' => 'Staff.Categories',
                    'placeholder' => 'staff.placeholder.stafftype',
                    'attr' => array(
                        'class' => 'staffMember',
                    )
                )
            )
            ->add('jobTitle', null, array(
                    'label' => 'staff.label.jobTitle',
                    'attr' => array(
                        'class' => 'staffMember',
                    )
                )
            )
            ->add('house', SettingType::class, array(
                    'label' => 'family.label.house',
                    'placeholder' => 'family.placeholder.house',
                    'required' => false,
                    'attr' => array(
                        'help' => 'family.help.house',
                    ),
                    'setting_name' => 'house.list',
                    'translation_domain' => 'BusybeeFamilyBundle',
                    'choice_translation_domain' => 'BusybeeFamilyBundle',
                )
            );
        $builder->get('person')->addModelTransformer(new EntityToIntTransformer($this->manager, Person::class));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Busybee\StaffBundle\Entity\Staff',
            'translation_domain' => 'BusybeeStaffBundle',
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