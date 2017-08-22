<?php

namespace Busybee\StaffBundle\Form;

use Busybee\Core\FormBundle\Type\SettingChoiceType;
use Busybee\InstituteBundle\Entity\Space;
use Busybee\PersonBundle\Entity\Person;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\StaffBundle\Entity\Staff;
use Busybee\StaffBundle\Events\StaffSubscriber;
use Busybee\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ->add('staffType', SettingChoiceType::class, array(
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
            ->add('house', SettingChoiceType::class, array(
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
            )
            ->add('homeroom', EntityType::class, array(
                    'label' => 'staff.label.homeroom',
                    'class' => Space::class,
                    'choice_label' => 'name',
                    'placeholder' => 'staff.placeholder.homeroom',
                    'required' => false,
                    'attr' => array(
                        'help' => 'staff.help.homeroom',
                    ),
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('h')
                            ->leftJoin('h.staff', 's')
                            ->where('s.person = :person_id')
                            ->orWhere('h.staff IS NULL')
                            ->setParameter('person_id', $options['person_id'])
                            ->orderBy('h.name', 'ASC');
                    },
                )
            );
        $builder->get('person')->addModelTransformer(new EntityToStringTransformer($this->manager, Person::class));
        $builder->addEventSubscriber(new StaffSubscriber($this->manager));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Staff::class,
                'translation_domain' => 'BusybeeStaffBundle',
            ]
        );
        $resolver->setRequired(
            [
                'person_id',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'staff';
    }
}