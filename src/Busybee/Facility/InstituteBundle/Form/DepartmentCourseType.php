<?php

namespace Busybee\Facility\InstituteBundle\Form;

use Busybee\Program\CurriculumBundle\Entity\Course;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartmentCourseType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('id', EntityType::class,
				[
					'label'         => 'department.course.label.name',
					'class'         => Course::class,
					'choice_label'  => 'name',
					'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('c')
							->orderBy('c.name', 'ASC');
					},
					'placeholder'   => 'department.course.placeholder.name',
				]
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class'         => Course::class,
			'translation_domain' => 'BusybeeInstituteBundle',
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'departmentCourse';
	}
}
