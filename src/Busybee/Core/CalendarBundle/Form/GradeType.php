<?php

namespace Busybee\Core\CalendarBundle\Form;

use Busybee\Core\FormBundle\Type\SettingChoiceType;
use Busybee\Core\CalendarBundle\Entity\Grade;
use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\Core\CalendarBundle\Events\GradeSubscriber;
use Busybee\Core\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GradeType extends AbstractType
{
	/**
	 * @var ObjectManager
	 */
	private $om;

	/**
	 * @var SettingManager
	 */
	private $sm;

	/**
	 * DepartmentType constructor.
	 *
	 * @param ObjectManager  $om
	 * @param SettingManager $sm
	 */
	public function __construct(ObjectManager $om, SettingManager $sm)
	{
		$this->om = $om;
		$this->sm = $sm;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('grade', SettingChoiceType::class,
				[
					'label'        => 'grade.label.grade',
					'setting_name' => 'student.groups',
					'required'     => true,
					'placeholder'  => 'grade.placeholder.grade',
				]
			)
			->add('name', HiddenType::class)
			->add('year', HiddenType::class)
			->add('sequence', HiddenType::class);

		$builder->get('year')->addModelTransformer(new EntityToStringTransformer($this->om, Year::class));
		$builder->addEventSubscriber(new GradeSubscriber($this->sm));
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class'         => Grade::class,
				'translation_domain' => 'BusybeeCalendarBundle',
				'year_data'          => null,
				'error_bubbling'     => true,
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'grade';
	}

	/**
	 * @param FormView      $view
	 * @param FormInterface $form
	 * @param array         $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['year_data'] = $options['year_data'];
	}
}
