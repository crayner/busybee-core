<?php

namespace Busybee\Core\CalendarBundle\Form;

use Busybee\Core\CalendarBundle\Model\GradeManager;
use Busybee\Core\TemplateBundle\Type\HiddenEntityType;
use Busybee\Core\TemplateBundle\Type\SettingChoiceType;
use Busybee\Core\CalendarBundle\Entity\Grade;
use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\Core\CalendarBundle\Events\GradeSubscriber;
use Busybee\Core\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GradeType extends AbstractType
{
	/**
	 * @var ObjectManager
	 */
	private $objectManager;

	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * @var GradeManager
	 */
	private $gradeManager;

	/**
	 * DepartmentType constructor.
	 *
	 * @param                ObjectManager  objectManager
	 * @param SettingManager $settingManager
	 */
	public function __construct(ObjectManager $objectManager, SettingManager $settingManager, GradeManager $gradeManager)
	{
		$this->objectManager  = $objectManager;
		$this->settingManager = $settingManager;
		$this->gradeManager   = $gradeManager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('grade', SettingChoiceType::class,
				[
					'label'        => 'grade.name.select',
					'setting_name' => 'student.groups',
					'required'     => true,
					'placeholder'  => 'grade.placeholder.grade',
				]
			)
			->add('name', HiddenType::class)
			->add('year', HiddenEntityType::class,
				[
					'class' => Year::class,
				]
			)
			->add('sequence', HiddenType::class)
			->add('website', UrlType::class,
				[
					'label'    => 'grade.website.label',
					'required' => false,
				]
			);

		$builder->addEventSubscriber(new GradeSubscriber($this->settingManager, $this->gradeManager));
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
		$resolver->setRequired(
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
		$view->vars['manager']   = $options['manager'];
	}
}
