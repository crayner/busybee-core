<?php

namespace Busybee\RecordBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecordType extends AbstractType
{
	
	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder
            ->add('save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label' 				=> 'form.save',
					'translation_domain' 	=> 'BusybeeDisplayBundle',
					'attr' 					=> array(
						'class' 				=> 'btn btn-success glyphicon glyphicon-save'
					),
				)
			)
            ->add('save_and_add', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label' 				=> 'form.save_and_add',
					'translation_domain' 	=> 'BusybeeDisplayBundle',
					'attr' 					=> array(
						'class' 				=> 'btn btn-success glyphicon glyphicon-plus-sign'
					),
				)
			)
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
			->setDefaults(
				array(
					'data_class' 				=> null,
					'translation_domain'		=> 'BusybeeRecordBundle',
					'attr'					=> array(
						'novalidator'			=> 'novalidator',
					),
        		)
			)
		;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'database_record';
    }
	
}
