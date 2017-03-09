<?php

namespace Busybee\InstituteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class StudentYearType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null,
                array(
                    'label' => 'groups.year.label.name',
                    'attr' => array(
                        'help' => 'groups.year.help.name'
                    ),
                )
            )
            ->add('nameShort', null,
                array(
                    'label' => 'groups.year.label.nameShort',
                    'attr' => array(
                        'help' => 'groups.year.help.nameShort'
                    ),
                )
            )
            ->add('sequence', IntegerType::class,
                array(
                    'label' => 'groups.year.label.sequence',
                    'attr' => array(
                        'help' => 'groups.year.help.sequence'
                    ),
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
                'data_class' => 'Busybee\InstituteBundle\Entity\StudentYear',
                'translation_domain' => 'BusybeeInstituteBundle',
                'validation_groups' => array('Default')
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'studentyear';
    }


}
