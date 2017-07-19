<?php

namespace Busybee\StudentBundle\Form;

use Busybee\StudentBundle\Events\StudentGradesSubscriber;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
        $resolver
            ->setRequired(
                [
                    'year_data',
                    'manager',
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

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['year_data'] = $options['year_data'];
        $view->vars['manager'] = $options['manager'];
    }
}
