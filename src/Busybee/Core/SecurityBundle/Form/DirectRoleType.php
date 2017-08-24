<?php

namespace Busybee\Core\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DirectRoleType extends AbstractType
{
	/**
	 * @var array
	 */
	private $roles;

	/**
	 * GroupType constructor.
	 *
	 * @param array $groups
	 */
	public function __construct($roles)
	{
		$this->roles = $roles;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'user_directrole';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'user_directrole';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getParent()
	{
		return ChoiceType::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'label'              => 'user.label.directroles',
				'multiple'           => true,
				'expanded'           => true,
				'required'           => false,
				'attr'               => array(
					'help'  => 'user.help.directroles',
					'class' => 'user',
				),
				'translation_domain' => 'BusybeeSecurityBundle',
				'choices'            => $this->getRoleChoices(),
			)
		);
	}

	/**
	 * get Role Choices
	 *
	 * @version 11th March 2017
	 * @return array
	 */
	private function getRoleChoices()
	{
		$roles = [];
		foreach ($this->roles as $role => $subRoles)
			$roles[$role] = $role;

		return $roles;
	}
}