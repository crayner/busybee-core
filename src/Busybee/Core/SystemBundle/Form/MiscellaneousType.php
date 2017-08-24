<?php

namespace Busybee\Core\SystemBundle\Form;

use Busybee\Core\FormBundle\Type\TextType;
use Busybee\Core\FormBundle\Type\ToggleType;
use Busybee\Core\SecurityBundle\Validator\Password;
use Busybee\Core\SystemBundle\Event\MiscellaneousSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class MiscellaneousType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('secret', HiddenType::class)
			->add('session_name', TextType::class,
				[
					'label'       => 'misc.session_name.label',
					'attr'        => array(
						'help' => 'misc.session_name.help',
					),
					'mapped'      => false,
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('session_max_idle_time', TextType::class,
				[
					'label'       => 'misc.session_max_idle_time.label',
					'attr'        => array(
						'help' => 'misc.session_max_idle_time.help',
					),
					'mapped'      => false,
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('signin_count_minimum', TextType::class,
				[
					'label'       => 'misc.signin_count_minimum.label',
					'attr'        => array(
						'help' => 'misc.signin_count_minimum.help',
					),
					'mapped'      => false,
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('session_remember_me_name', TextType::class,
				[
					'label'       => 'misc.session_remember_me_name.label',
					'attr'        => array(
						'help' => 'misc.session_remember_me_name.help',
					),
					'mapped'      => false,
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('country', CountryType::class,
				[
					'label'       => 'misc.country.label',
					'attr'        => array(
						'help' => 'misc.country.help',
					),
					'mapped'      => false,
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('timezone', TimezoneType::class,
				[
					'label'       => 'misc.timezone.label',
					'attr'        => array(
						'help' => 'misc.timezone.help',
					),
					'mapped'      => false,
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('locale', LocaleType::class,
				[
					'label'       => 'misc.locale.label',
					'attr'        => array(
						'help' => 'misc.locale.help',
					),
					'mapped'      => false,
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('minLength', ChoiceType::class,
				[
					'label'   => 'misc.password.minLength.label',
					'attr'    => array(
						'help' => 'misc.password.minLength.help',
					),
					'mapped'  => false,
					'choices' => [
						8  => 8,
						9  => 9,
						10 => 10,
						11 => 11,
						12 => 12,
						13 => 13,
						14 => 14,
						15 => 15,
						16 => 16,
					],
				]
			)
			->add('numbers', ToggleType::class,
				[
					'label'  => 'misc.password.numbers.label',
					'attr'   => array(
						'help' => 'misc.password.numbers.help',
					),
					'mapped' => false,
				]
			)
			->add('mixedCase', ToggleType::class,
				[
					'label'  => 'misc.password.mixedCase.label',
					'attr'   => array(
						'help' => 'misc.password.mixedCase.help',
					),
					'mapped' => false,
				]
			)
			->add('specials', ToggleType::class,
				[
					'label'  => 'misc.password.specials.label',
					'attr'   => array(
						'help' => 'misc.password.specials.help',
					),
					'mapped' => false,
				]
			)
			->add('email', EmailType::class,
				[
					'label'       => 'misc.email.label',
					'attr'        => array(
						'help' => 'misc.email.help',
					),
					'mapped'      => false,
					'constraints' => [
						new NotBlank(),
						new Email(
							[
								'strict'  => true,
								'checkMX' => true,
							]
						),
					],
				]
			)
			->add('username', TextType::class,
				[
					'label'       => 'misc.username.label',
					'attr'        => array(
						'help' => 'misc.username.help',
					),
					'mapped'      => false,
					'required'    => false,
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('pass_word', TextType::class,
				[
					'label'       => 'misc.password.label',
					'attr'        => array(
						'help' => 'misc.password.help',
					),
					'mapped'      => false,
					'constraints' => [
						new NotBlank(),
						new Password(),
					],
				]
			)
			->add('oauth', ToggleType::class,
				[
					'label'  => 'misc.google.oauth.label',
					'attr'   => array(
						'help' => 'misc.google.oauth.help',
					),
					'mapped' => false,
				]
			)
			->add('client_id', TextType::class,
				[
					'label'    => 'misc.google.client_id.label',
					'attr'     => array(
						'help'  => 'misc.google.client_id.help',
						'class' => 'googleSetting',
					),
					'mapped'   => false,
					'required' => false,
				]
			)
			->add('client_secret', TextType::class,
				[
					'label'    => 'misc.google.client_secret.label',
					'attr'     => array(
						'help'  => 'misc.google.client_secret.help',
						'class' => 'googleSetting',
					),
					'mapped'   => false,
					'required' => false,
				]
			);
		$builder->addEventSubscriber(new MiscellaneousSubscriber());
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'translation_domain' => 'SystemBundle',
			'data_class'         => null,
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'mailer';
	}


}
