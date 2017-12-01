<?php

namespace Busybee\People\StudentBundle\Form;

use Busybee\Core\CalendarBundle\Entity\CalendarGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarGroupType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', HiddenType::class)
			->add('nameShort', HiddenType::class)
			->add('sequence', HiddenType::class)
			->add('defaultStatus', ChoiceType::class,
				[
					'choices'     => $options['manager']->getSm()->get('student.enrolment.status'),
					'mapped'      => false,
					'label'       => 'calendar.group.defaultStatus.label',
					'attr'        =>
						[
							'help' => 'calendar.group.defaultStatus.help',
						],
					'placeholder' => 'calendar.group.defaultStatus.placeholder',
				]
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver
			->setDefaults(
				[
					'data_class'         => CalendarGroup::class,
					'translation_domain' => 'BusybeeStudentBundle',
				]
			);
		$resolver
			->setRequired(
				[
					'manager',
				]
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'student_to_calendar_group';
	}


}
