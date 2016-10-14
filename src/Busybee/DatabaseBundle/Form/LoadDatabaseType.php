<?php

namespace Busybee\DatabaseBundle\Form ;

use Symfony\Component\Form\AbstractType ;
use Symfony\Component\Form\FormBuilderInterface ;
use Symfony\Component\OptionsResolver\OptionsResolver ;

class LoadDatabaseType extends AbstractType
{
	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder
            ->add('name', 'Symfony\Component\Form\Extension\Core\Type\FileType', array(
					'label'					=> 'database.label.name',
					'help_block'			=> 'database.help.name',
				)
			)
            ->add('refresh', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
					'label'					=> 'database.label.refresh',
					'help_block'			=> 'database.help.refresh',
					'data'					=> false,
					'required'				=> false,
					'render_optional_text' 	=> false,
				)
			)
	        ->add('upload', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label' 				=> 'form.upload',
					'translation_domain' 	=> 'BusybeeDisplayBundle',
					'attr' 					=> array(
						'class' 				=> 'btn btn-success glyphicon glyphicon-upload'
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
			->setDefaults(array(
            	'data_class' 				=> 'Busybee\DatabaseBundle\Entity\File',
				'translation_domain'		=> 'BusybeeDatabaseBundle',
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
        return 'database_load';
    }
	
}
