<?php

namespace General\ValidationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaginationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('offset', 'hidden')
            ->add('limit', 'hidden')
            ->add('total', 'hidden')
            ->add('section', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'validation.pagination.search.group',
					'translation_domain' 	=> 'GeneralValidationBundle',
					'required'				=> false,
					'render_optional_text'	=> false,
				)
			)
            ->add('validator', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'validation.pagination.search.name',
					'translation_domain' 	=> 'GeneralValidationBundle',
					'required'				=> false,
					'render_optional_text'	=> false,
				)
			)
			->add('search', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label'				=> 'form.search',
					'attr' 					=> array(
						'class' 				=> 'btn btn-success glyphicon glyphicon-search'
					),
				)
			)
			->add('next', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label'				=> 'form.next',
					'attr' 					=> array(
						'class' 				=> 'btn btn-info glyphicon glyphicon-forward'
					),
				)
			)
			->add('prev', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label'				=> 'form.prev',
					'attr' 					=> array(
						'class' 				=> 'btn btn-info glyphicon glyphicon-backward'
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
        $resolver->setDefaults(array(
				'translation_domain' 	=> 'BusybeeDisplayBundle',
				'validation_groups'		=> null,
				'attr'					=> array(
					'class'					=> 'ajaxForm',
					'novalidator'			=> 'novalidator',
				),
			)
		);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'general_paginator';
    }
	
	/**
	 * @return
	 */
	public function handleRequest($request)
	{
		dump($this);
		parent::handleRequest($request);
	}
}
