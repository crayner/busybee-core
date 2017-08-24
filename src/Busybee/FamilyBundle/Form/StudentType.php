<?php

namespace Busybee\FamilyBundle\Form;

use Busybee\People\PersonBundle\Entity\Person;
use Busybee\People\StudentBundle\Entity\Student;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('person', EntityType::class, array(
                    'label' => 'students.label.person',
                    'class' => Person::class,
                    'choice_label' => 'formatName',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('p')
                            ->where('p.studentQuestion = 1')
                            ->addOrderBy('p.surname', 'ASC')
                            ->addOrderBy('p.firstName', 'ASC');
                    },
                    'placeholder' => 'students.placeholder.person',
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'student';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'student';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Student::class,
                'translation_domain' => 'BusybeeFamilyBundle',
            )
        );
    }
}