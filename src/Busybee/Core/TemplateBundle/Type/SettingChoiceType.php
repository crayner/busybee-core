<?php

namespace Busybee\Core\TemplateBundle\Type;

use Busybee\Core\TemplateBundle\Events\SettingChoiceSubscriber;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class SettingChoiceType extends AbstractType
{
	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * @var TranslatorInterface
	 */
	private $translator;

	/**
	 * SettingType constructor.
	 *
	 * @param SettingManager $settingManager
	 */
	public function __construct(SettingManager $settingManager, TranslatorInterface $translator)
	{
		$this->settingManager = $settingManager;
		$this->translator     = $translator;
	}

	/**
	 * @return string
	 */
	public function getBlockPrefix()
	{
		return 'setting_choice';
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setRequired(
			array(
				'setting_name',
			)
		);
		$resolver->setDefaults(
			array(
				'expanded'           => false,
				'multiple'           => false,
				'placeholder'        => null,
				'year_data'          => null,
				'use_label_as_value' => false,
			)
		);
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->addEventSubscriber(new SettingChoiceSubscriber($this->settingManager, $this->translator));
	}

	/**
	 * @param FormView      $view
	 * @param FormInterface $form
	 * @param array         $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['setting_name']       = $options['setting_name'];
		$view->vars['use_label_as_value'] = $options['use_label_as_value'];
	}

	/**
	 * @return string
	 */
	public function getParent()
	{
		return ChoiceType::class;
	}
}