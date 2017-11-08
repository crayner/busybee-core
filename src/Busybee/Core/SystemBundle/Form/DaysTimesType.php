<?php

namespace Busybee\Core\SystemBundle\Form;

use Busybee\Core\SystemBundle\Model\DaysTimesManager;
use Busybee\Core\SystemBundle\Validator\Times;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DaysTimesType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('day', DayType::class, [])
			->add('time', TimeType::class,
				[
					'constraints' => [
						new Times(),
					],
				]
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'translation_domain' => 'SystemBundle',
			'data_class'         => DaysTimesManager::class,
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'days_times_manage';
	}
}
