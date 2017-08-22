<?php

namespace Busybee\Core\FormBundle\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType as TimeCoreType;

class TimeType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'compound' => true,
				'multiple' => false,
			)
		);
	}

	public function getBlockPrefix()
	{
		return 'bee_time';
	}

	public function getParent()
	{
		return TimeCoreType::class;
	}
}