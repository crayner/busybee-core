<?php

namespace Busybee\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Busybee\SecurityBundle\Form\Choices ;

class RegisterType extends AbstractType
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
					'attr'					=> array(
							'help' 			=> 'user.help.username',
						),
					'required' 				=> false,
				)
			)
			->add('plainPassword', 'Symfony\Component\Form\Extension\Core\Type\RepeatedType', array(
					'type' 					=> 'Symfony\Component\Form\Extension\Core\Type\PasswordType',
					'first_options' 		=> array(
						'label' 				=> 'user.label.password'
					),
					'second_options' 		=> array(
						'label' 				=> 'user.label.password_confirmation'
					),
					'invalid_message' 		=> 'user.error.password.match',
				)
			)
            ->add('email', 'Symfony\Component\Form\Extension\Core\Type\EmailType', array(
					'label' 				=> 'user.label.email',
					'required'				=> true,
					'attr'					=> array(
							'help' 			=> 'user.help.email',
						),
				)
			)
            ->add('directroles', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
					'label' 				=> 'user.label.directroles',
					'multiple' 				=> true,
					'expanded' 				=> true,
					'class' 				=> 'Busybee\SecurityBundle\Entity\Role',
					'choice_label' 			=> 'role',
					'required' 				=> false,
					'attr'					=> array(
							'help' 			=> 'user.help.directroles',
						),
				)
			)
            ->add('groups', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
					'multiple' 				=> true,
					'expanded' 				=> true,
					'class' 				=> 'Busybee\SecurityBundle\Entity\Group',
					'choice_label' 			=> 'groupname',
					'label' 				=> 'user.label.groups',
					'required' 				=> true,
					'attr'					=> array(
							'help' 			=> 'user.help.groups',
						),
				)
			)
			->add('register', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label' 				=> 'form.register',
					'translation_domain' 	=> 'BusybeeHomeBundle',
					'attr' 					=> array(
						'class' 				=> 'btn btn-success glyphicons glyphicons-disk-save'
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
				'validation_groups' 	=> null,
					'attr'				=> array(
						'class'				=> 'ajaxForm',
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
        return 'bee_security_register';
    }
}
