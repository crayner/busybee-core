<?php

namespace Busybee\Core\SecurityBundle\Form;

use Busybee\Core\CalendarBundle\Form\YearEntityType;
use Busybee\Core\SecurityBundle\Entity\User;
use Busybee\Core\TemplateBundle\Type\TextType;
use Busybee\Core\TemplateBundle\Type\ToggleType;
use Busybee\Core\SecurityBundle\Event\UserSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$years = [];
		$year  = intval(date('Y', strtotime('now')));
		for ($y = 0; $y < 5; $y++)
			$years[] = strval($year + $y);
		if (!is_null($options['data']->getCredentialsExpireAt()))
		{
			$years[] = $options['data']->getCredentialsExpireAt()->format('Y');
			$years   = array_unique($years);
			asort($years);
		}
		if (!is_null($options['data']->getExpiresAt()))
		{
			$years[] = $options['data']->getExpiresAt()->format('Y');
			$years   = array_unique($years);
			asort($years);
		}
		$builder
			->add('username', TextType::class, array(
					'label'    => 'user.label.username',
					'attr'     => array(
						'help'  => 'user.help.username',
						'class' => 'user',
					),
					'required' => false,
				)
			)
			->add('usernameCanonical', HiddenType::class,
				array(
					'attr' => array(
						'class' => 'user',
					),
				)
			)
			->add('email', TextType::class, array(
					'attr'  => array(
						'class' => 'user',
						'help'  => 'user.email.help',
					),
					'label' => 'user.email.label',
				)
			)
			->add('emailCanonical', HiddenType::class, array(
					'attr' => array(
						'class' => 'user',
					),
				)
			)
			->add('enabled', ToggleType::class,
				array(
					'label' => 'user.label.enabled',
					'attr'  => array(
						'help'      => 'user.help.enabled',
						'class'     => 'user',
						'data-size' => 'mini',
					),
				)
			)
			->add('locale', LocaleType::class,
				array(
					'label'    => 'user.label.locale',
					'attr'     => array(
						'help'  => 'user.help.locale',
						'class' => 'user',
					),
					'required' => false,
				)
			)
			->add('password', HiddenType::class,
				array(
					'attr' => array(
						'class' => 'user',
					)
				)
			)
			->add('locked', ToggleType::class,
				array(
					'label' => 'user.label.locked',
					'attr'  => array(
						'help'      => 'user.help.locked',
						'class'     => 'user',
						'data-size' => 'mini',
					),
				)
			)
			->add('expired', ToggleType::class,
				array(
					'label' => 'user.label.expired',
					'attr'  => array(
						'help'      => 'user.help.expired',
						'class'     => 'user',
						'data-size' => 'mini',
					),
				)
			)
			->add('expiresAt', DateType::class,
				[
					'label'       => 'user.expiresAt.label',
					'attr'        => [
						'help'  => 'user.expiresAt.help',
						'class' => 'user',
					],
					'years'       => $years,
					'placeholder' => [
						'year' => '', 'month' => '', 'day' => '',
					],
					'required'    => false,
				]
			)
			->add('credentials_expired', ToggleType::class,
				array(
					'label' => 'user.label.credentials_expired',
					'attr'  => array(
						'help'      => 'user.help.credentials_expired',
						'class'     => 'user',
						'data-size' => 'mini',
					),
				)
			)
			->add('credentialsExpireAt', DateType::class,
				array(
					'label'       => 'user.credentialsExpireAt.label',
					'attr'        => [
						'help'  => 'user.credentialsExpireAt.help',
						'class' => 'user',
					],
					'years'       => $years,
					'placeholder' => [
						'year' => '', 'month' => '', 'day' => ''
					],
					'required'    => false,
				)
			)
			->add('year', YearEntityType::class, [
					'placeholder'        => 'user.placeholder.year',
					'label'              => 'user.label.year',
					'attr'               =>
						[
							'help' => 'user.help.year',
						],
					'required'           => false,
					'translation_domain' => 'BusybeeSecurityBundle',
				]
			);

		$builder->addEventSubscriber(new UserSubscriber($options['isSystemAdmin']));
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class'         => User::class,
				'translation_domain' => 'BusybeeSecurityBundle',
			)
		);
		$resolver->setRequired(
			[
				'isSystemAdmin',
			]
		);
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'user';
	}
}
