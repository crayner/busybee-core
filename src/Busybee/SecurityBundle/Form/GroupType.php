<?php

namespace Busybee\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('groupname', 'Symfony\Component\Form\Extension\Core\Type\TextType',  array(
					'label' => 'group.label.name'
				)
			)
            ->add('roles', 'Symfony\Bridge\Doctrine\Form\Type\EntityType',  array(
					'label' 				=> 'group.label.roles.assigned',
					'choice_label'			=> 'role',
					'multiple' 				=> true,
					'expanded' 				=> true,
					'help_label' 			=> 'group.help.roles.assigned',
					'required' 				=> true,
					'class' 				=> 'Busybee\SecurityBundle\Entity\Role',
				)
			)
            ->add('save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(	
					'label' 				=> 'form.save',
					'translation_domain' 	=> 'BusybeeDisplayBundle',
					'attr' 					=> array(
						'class'					=> 'btn btn-success glyphicon glyphicon-save'
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
            ->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label'					=> 'form.cancel', 
					'translation_domain' 	=> 'BusybeeDisplayBundle',
					'attr' 					=> array(
						'formnovalidate' 		=> 'formnovalidate',
						'class' 				=> 'btn btn-info glyphicons glyphicons-exclamation-sign',
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
            'data_class' 			=> 'Busybee\SecurityBundle\Entity\Group',
            'translation_domain' 	=> 'BusybeeSecurityBundle',
            'validation_groups' 	=> null,
        )
		);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bee_security_group';
    }
}
