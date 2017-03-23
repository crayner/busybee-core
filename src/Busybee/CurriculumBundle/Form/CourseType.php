<?php

namespace Busybee\CurriculumBundle\Form;

use Busybee\CurriculumBundle\Entity\Course;
use Busybee\FormBundle\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
                array(
                    'label' => 'course.label.name',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('version', TextType::class,
                array(
                    'label' => 'course.label.version',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('changeRecord', EntityType::class,
                array(
                    'label' => false,
                    'attr' => array(
                        'class' => 'formChanged changeRecord',
                    ),
                    'class' => Course::class,
                    'choice_label' => 'nameVersion',
                    'choice_value' => 'id',
                    'mapped' => false,
                    'required' => false,
                    'placeholder' => 'course.placeholder.changeRecord',
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Course::class,
                'translation_domain' => 'BusybeeCurriculumBundle',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'course';
    }


}
