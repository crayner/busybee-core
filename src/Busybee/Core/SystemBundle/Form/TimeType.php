<?php

namespace Busybee\Core\SystemBundle\Form;

use Busybee\Core\SystemBundle\Model\DaysTimesManager;
use Busybee\Core\SystemBundle\Model\Time;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TimeType extends AbstractType
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
			->add('open', \Busybee\Core\TemplateBundle\Type\TimeType::class,
				[
					'label'       => 'school.admin.day_time.open.label',
					'constraints' => [
						new NotBlank(),
					],
					'attr'        => [
						'help' => 'school.admin.day_time.open.help',
					],
				]
			)
			->add('begin', \Busybee\Core\TemplateBundle\Type\TimeType::class,
				[
					'label'       => 'school.admin.day_time.begin.label',
					'constraints' => [
						new NotBlank(),
					],
					'attr'        => [
						'help' => 'school.admin.day_time.begin.help',
					],
				]
			)
			->add('finish', \Busybee\Core\TemplateBundle\Type\TimeType::class,
				[
					'label'       => 'school.admin.day_time.finish.label',
					'constraints' => [
						new NotBlank(),
					],
					'attr'        => [
						'help' => 'school.admin.day_time.finish.help',
					],
				]
			)
			->add('close', \Busybee\Core\TemplateBundle\Type\TimeType::class,
				[
					'label'       => 'school.admin.day_time.close.label',
					'constraints' => [
						new NotBlank(),
					],
					'attr'        => [
						'help' => 'school.admin.day_time.close.help',
					],
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
				'data_class'         => Time::class,
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'school_time';
	}
}
