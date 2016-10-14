<?php

namespace Busybee\DatabaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\Request ;

class TableType extends AbstractType
{
	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder
            ->add('name', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'table.label.name',
				)
			)
            ->add('limits', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
					'label'					=> 'table.label.limits',
					'help_block'			=> 'table.help.limits',
					'choices'				=> $options['data']->getLimitChoices(),
				)
			)
            ->add('role', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
					'label'					=> 'table.label.role',
					'help_block'			=> 'table.help.role',
					'placeholder'			=> 'table.placeholder.role',
					'class'					=> 'Busybee\SecurityBundle\Entity\Role',
					'choice_label'			=> 'role',
					'multiple'				=> false,
					'expanded'				=> false,
					'required'				=> false,
					'data'					=> $options['data']->getSelectRole(),
				)
			)
            ->add('parent', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
					'label'					=> 'table.label.parent',
					'help_block'			=> 'table.help.parent',
					'class'					=> 'Busybee\DatabaseBundle\Entity\Table',
					'choice_label'			=> 'name',
					'multiple'				=> false,
					'expanded'				=> false,
					'required'				=> false,
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
					'data_class' 				=> 'Busybee\DatabaseBundle\Entity\Table',
					'translation_domain'		=> 'BusybeeDatabaseBundle',
					'attr'						=> array(
						'class'						=> 'form-horizontal',
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
        return 'database_table';
    }
}
