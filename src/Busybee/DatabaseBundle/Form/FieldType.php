<?php

namespace Busybee\DatabaseBundle\Form ;

use Symfony\Component\Form\AbstractType ;
use Symfony\Component\Form\FormBuilderInterface ;
use Symfony\Component\OptionsResolver\OptionsResolver ;
use Busybee\DatabaseBundle\Entity\EnumeratorRepository ;

class FieldType extends AbstractType
{
	private $class = '\Busybee\DatabaseBundle\Form\FieldType';
	
	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder
            ->add('name', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'field.label.name',
					'data'					=> $options['data']->getDisplayName(),
				)
			)
            ->add('type', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
					'label'					=> 'field.label.type',
					'choices'				=> $options['data']->getTypes($options['data']->enumeratorRepository),
					'help_block'			=> 'field.help.type',
					'placeholder'			=> 'field.placeholder.type',
					'multiple'				=> false,
					'expanded'				=> false,
					'data'					=> $options['data']->getType(),
				)
			)
            ->add('table', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
					'label'					=> 'field.label.table',
					'help_block'			=> 'field.help.table',
					'placeholder'			=> 'field.placeholder.table',
					'class'					=> 'Busybee\DatabaseBundle\Entity\Table',
					'choice_label'			=> 'name',
					'multiple'				=> false,
					'expanded'				=> false,
					'data'					=> $options['data']->getSelectTable(),
				)
			)
            ->add('role', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
					'label'					=> 'field.label.role',
					'help_block'			=> 'field.help.role',
					'placeholder'			=> 'field.placeholder.role',
					'class'					=> 'Busybee\SecurityBundle\Entity\Role',
					'choice_label'			=> 'role',
					'multiple'				=> false,
					'expanded'				=> false,
					'required'				=> false,
					'data'					=> $options['data']->getSelectRole(),
				)
			)
			->add('validator', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array(
					'label'					=> 'field.label.validator',
					'help_block'			=> 'field.help.validator',
				)
			)
			->add('prompt', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'field.label.prompt',
					'help_block'			=> 'field.help.prompt',
					'required'				=> false,
					'render_optional_text'	=> false,
				)
			)
			->add('parameters', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array(
					'label'					=> 'field.label.parameters',
					'help_block'			=> 'field.help.parameters',
					'required'				=> false,
				)
			)
			->add('sortkey', 'Symfony\Component\Form\Extension\Core\Type\HiddenType')
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
			->setDefaults(array(
            	'data_class' 				=> 'Busybee\DatabaseBundle\Entity\Field',
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
        return 'database_field';
    }
}
