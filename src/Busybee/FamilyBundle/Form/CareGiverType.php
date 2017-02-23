<?php

namespace Busybee\FamilyBundle\Form;

use Busybee\FamilyBundle\Entity\CareGiver;
use Busybee\PersonBundle\Entity\Person;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CareGiverType extends AbstractType
{
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
                            ->orderBy('p.surname', 'ASC')
                            ->orderBy('p.firstName', 'ASC');
                    },
                    'placeholder' => 'caregiver.placeholder.person',
                )
            );
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
        ));
    }
}