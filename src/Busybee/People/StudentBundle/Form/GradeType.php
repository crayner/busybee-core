<?php

namespace Busybee\People\StudentBundle\Form;

use Busybee\Core\CalendarBundle\Entity\Grade;
use Busybee\Core\TemplateBundle\Type\EntityType;
use Busybee\People\StudentBundle\Entity\StudentGrade;
use Busybee\People\StudentBundle\Events\StudentGradeEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GradeType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', HiddenType::class)
			->add('grade', HiddenType::class)
			->add('sequence', HiddenType::class)
			/*			->add('students', EntityType::class,
							[
								'class' => StudentGrade::class,
								'multiple' => true,
								'choices' => $options['manager']->getPossibleStudents(),
								'choice_value' => 'studentId',
								'choice_label' => 'studentName',
								'expanded' => true,
								'label' => 'grade.students.label',
								'required' => false,
							]
						)
			*/
			->add('defaultStatus', ChoiceType::class,
				[
					'choices'     => $options['manager']->getSm()->get('student.enrolment.status'),
					'mapped'      => false,
					'label'       => 'grade.defaultStatus.label',
					'attr'        =>
						[
							'help' => 'grade.defaultStatus.help',
						],
					'placeholder' => 'grade.defaultStatus.placeholder',
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
					'data_class'         => Grade::class,
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
		return 'add_students_to_grade';
	}


}
