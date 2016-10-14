<?php

namespace Busybee\InstituteBundle\Form ;

use Symfony\Component\Form\AbstractType ;
use Symfony\Component\Form\FormBuilderInterface ;
use Symfony\Component\OptionsResolver\OptionsResolver ;

class InstituteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifier', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'institute.label.identifier',
					'help_block'			=> 'institute.help.identifier',
				)
			)
            ->add('name', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'institute.label.name',
				)
			)
            ->add('address_line_one', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'institute.label.address_line_one',
				)
			)
            ->add('address_line_two', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'institute.label.address_line_two',
					'help_block'			=> 'institute.help.address_line_two',
				)
			)
            ->add('locality', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'institute.label.locality',
					'help_block'			=> 'institute.help.locality',
				)
			)
            ->add('postcode', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'institute.label.postcode',
				)
			)
            ->add('state', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'institute.label.state',
				)
			)
            ->add('contact', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'institute.label.contact',
					'help_block'			=> 'institute.help.contact',
				)
			)
            ->add('phone', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'institute.label.phone',
				)
			)
            ->add('facsimile', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'institute.label.facsimile',
				)
			)
            ->add('email', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'institute.label.email',
				)
			)
            ->add('save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label' 				=> 'form.save',
					'translation_domain' 	=> 'BusybeeDisplayBundle',
					'attr' 					=> array(
						'class' 				=> 'btn btn-success glyphicon glyphicon-save'
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
				'data_class' 			=> 'Busybee\InstituteBundle\Entity\Institute',
				'translation_domain'	=> 'BusybeeInstituteBundle',
				'validation_groups'		=> null,
				'attr'					=> array(
					'class'					=> 'ajaxForm',
					'novalidate'			=> 'novalidate',
				),
			)
		);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'busybee_institute';
    }
}
