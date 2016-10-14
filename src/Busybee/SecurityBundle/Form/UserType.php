<?php

namespace Busybee\SecurityBundle\Form;

use Busybee\SecurityBundle\Entity\RoleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('username', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label' 				=> 'user.label.username',
					'help_block' 			=> 'user.help.username',
					'required' 				=> false,
				)
			)
            ->add('email', 'Symfony\Component\Form\Extension\Core\Type\EmailType', array(
					'label' 				=> 'user.label.email',
				)
			)
            ->add('directroles', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
					'label' 				=> 'user.label.directroles',
					'multiple' 				=> true,
					'expanded' 				=> true,
					'class' 				=> 'Busybee\SecurityBundle\Entity\Role',
					'choice_label' 			=> 'role',
					'required' 				=> false,
					'help_block' 			=> 'user.help.directroles',
				)
			)
            ->add('groups', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
					'multiple' 				=> true,
					'expanded' 				=> true,
					'class' 				=> 'Busybee\SecurityBundle\Entity\Group',
					'choice_label' 			=> 'groupname',
					'label' 				=> 'user.label.groups',
					'required' 				=> false,
					'help_block' 			=> 'user.help.groups',
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
				'data_class' 			=> 'Busybee\SecurityBundle\Entity\User',
				'translation_domain' 	=> 'BusybeeSecurityBundle',
				'validation_groups'		=> null,
				'attr'					=> array(
					'class'					=> 'ajaxForm',
				),
			)
		);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bee_security_user';
    }
}
