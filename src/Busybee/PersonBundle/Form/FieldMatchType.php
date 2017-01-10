<?php

namespace Busybee\PersonBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class FieldMatchType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $headerNames = $options['data']['headerNames'];
        $builder
            ->add('source', ChoiceType::class,
                array(
                    'mapped' => false,
                    'choices' => array_flip($headerNames->toArray()),
                    'required' => false,
                    'placeholder' => 'people.matchimport.placeholder.source',
                )
            )
            ->add('destination', ChoiceType::class,
                array(
                    'choices' => array_flip($options['data']['destinationNames']),
                    'mapped' => false,
                    'placeholder' => 'people.matchimport.placeholder.destination',
                    'required' => false,
                )
            )
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
			array(
				'data_class' => null,
				'translation_domain' => 'BusybeePersonBundle',
			)
		);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'field_match';
    }


}
