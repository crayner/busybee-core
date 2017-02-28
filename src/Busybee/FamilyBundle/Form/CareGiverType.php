<?php

namespace Busybee\FamilyBundle\Form;

use Busybee\FamilyBundle\Entity\CareGiver;
use Busybee\FamilyBundle\Entity\Family;
use Busybee\FormBundle\Type\ToggleType;
use Busybee\PersonBundle\Entity\Person;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CareGiverType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('person', EntityType::class, array(
                    'label' => 'caregiver.label.person',
                    'class' => Person::class,
                    'choice_label' => 'formatName',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('p')
                            ->where('p.studentQuestion = 0')
                            ->addOrderBy('p.surname', 'ASC')
                            ->addOrderBy('p.firstName', 'ASC');
                    },
                    'placeholder' => 'caregiver.placeholder.person',
                )
            )
            ->add('emailContact', ToggleType::class, array(
                    'label' => 'caregiver.label.emailcontact',
                    'attr' => array(
                        'data-size' => 'mini',
                    ),
                )
            )
            ->add('smsContact', ToggleType::class, array(
                    'label' => 'caregiver.label.smscontact',
                    'attr' => array(
                        'data-size' => 'mini',
                    ),
                )
            )
            ->add('mailContact', ToggleType::class, array(
                    'label' => 'caregiver.label.mailcontact',
                    'attr' => array(
                        'data-size' => 'mini',
                    ),
                )
            )
            ->add('phoneContact', ToggleType::class, array(
                    'label' => 'caregiver.label.phonecontact',
                    'attr' => array(

                        'data-size' => 'mini',
                    ),
                )
            )
            ->add('family', HiddenType::class)
            ->add('contactPriority', HiddenType::class);
        $builder->get('family')->addModelTransformer(new EntityToStringTransformer($this->manager, Family::class));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'caregiver';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'caregiver';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CareGiver::class,
            'translation_domain' => 'BusybeeFamilyBundle',
            'currentOrder' => 0,
        ));
    }
}