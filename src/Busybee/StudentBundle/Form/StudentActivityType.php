<?php

namespace Busybee\StudentBundle\Form;

use Busybee\StudentBundle\Events\StudentGradesSubscriber;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentActivityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'data_class' => null,
                    'translation_domain' => 'BusybeeStudentBundle',
                    'error_bubbling' => true,
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'studentActivity';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return EntityType::class;
    }
}
