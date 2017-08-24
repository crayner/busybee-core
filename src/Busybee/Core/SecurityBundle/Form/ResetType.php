<?php

namespace Busybee\Core\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Busybee\Core\SecurityBundle\Validator\Password;

class ResetType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('plainPassword', 'Symfony\Component\Form\Extension\Core\Type\RepeatedType', array(
					'type'            => 'Symfony\Component\Form\Extension\Core\Type\PasswordType',
					'first_options'   => array(
						'label' => 'user.label.password',
					),
					'second_options'  => array(
						'label' => 'user.label.password_confirmation'
					),
					'invalid_message' => 'user.error.password.match',
				)
			);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class'         => 'Busybee\Core\SecurityBundle\Entity\User',
			'intention'          => 'resetting',
			'translation_domain' => 'BusybeeSecurityBundle',
			'validation_groups'  => array(),
		));
	}


	public function getName()
	{
		return 'bee_security_reset';
	}
}
