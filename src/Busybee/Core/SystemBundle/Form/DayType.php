<?php

namespace Busybee\Core\SystemBundle\Form;

use Busybee\Core\SystemBundle\Model\Day;
use Busybee\Core\SystemBundle\Model\DaysTimesManager;
use Busybee\Core\TemplateBundle\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DayType extends AbstractType
{
	/**
	 * @var DaysTimesManager
	 */
	private $dayTimeManager;

	/**
	 * DayType constructor.
	 *
	 * @param DaysTimesManager $dayTimeManager
	 */
	public function __construct(DaysTimesManager $dayTimeManager)
	{
		$this->dayTimeManager = $dayTimeManager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('sun', ToggleType::class,
				[
					'label' => 'school.admin.day_time.sun.label',
				]
			)
			->add('mon', ToggleType::class,
				[
					'label' => 'school.admin.day_time.mon.label',
				]
			)
			->add('tue', ToggleType::class,
				[
					'label' => 'school.admin.day_time.tue.label',
				]
			)
			->add('wed', ToggleType::class,
				[
					'label' => 'school.admin.day_time.wed.label',
				]
			)
			->add('thu', ToggleType::class,
				[
					'label' => 'school.admin.day_time.thu.label',
				]
			)
			->add('fri', ToggleType::class,
				[
					'label' => 'school.admin.day_time.fri.label',
				]
			)
			->add('sat', ToggleType::class,
				[
					'label' => 'school.admin.day_time.sat.label',
				]
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'translation_domain' => 'SystemBundle',
				'data_class'         => Day::class,
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'school_day';
	}
}
