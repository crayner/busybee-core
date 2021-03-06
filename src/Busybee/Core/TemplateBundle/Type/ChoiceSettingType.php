<?php

namespace Busybee\Core\TemplateBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoiceSettingType extends AbstractType
{

	/**
	 * @return string
	 */
	public function getParent()
	{
		return ChoiceType::class;
	}

	/**
	 * @return string
	 */
	public function getBlockPrefix()
	{
		return 'choice_setting';
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
	}

	/**
	 * @param FormView      $view
	 * @param FormInterface $form
	 * @param array         $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['setting_name'] = strtolower($options['setting_name']);
	}
}