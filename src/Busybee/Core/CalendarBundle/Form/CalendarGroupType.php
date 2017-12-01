<?php
namespace Busybee\Core\CalendarBundle\Form;

use Busybee\Core\CalendarBundle\Entity\CalendarGroup;
use Busybee\Core\CalendarBundle\Model\CalendarGroupManager;
use Busybee\Core\TemplateBundle\Type\HiddenEntityType;
use Busybee\Core\TemplateBundle\Type\SettingChoiceType;
use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\Core\CalendarBundle\Events\CalendarGroupSubscriber;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarGroupType extends AbstractType
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
	 * @var CalendarGroupManager
	 */
	private $manager;

	/**
	 * DepartmentType constructor.
	 *
	 * @param ObjectManager        $objectManager
	 * @param SettingManager       $settingManager
	 * @param CalendarGroupManager $manager
	 *
	 * @internal param ObjectManager $ObjectManager
	 */
	public function __construct(ObjectManager $objectManager, SettingManager $settingManager, CalendarGroupManager $manager)
	{
		$this->objectManager  = $objectManager;
		$this->settingManager = $settingManager;
		$this->manager        = $manager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('nameShort', SettingChoiceType::class,
				[
					'label'        => 'calendar.group.nameshort.label',
					'setting_name' => 'student.groups',
					'required'     => true,
					'placeholder'  => 'calendar.group.nameshort.placeholder',
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
					'label'    => 'calendar.group.website.label',
					'required' => false,
				]
			);

		$builder->addEventSubscriber(new CalendarGroupSubscriber($this->settingManager, $this->manager));
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class'         => CalendarGroup::class,
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
		return 'calendar_group';
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
