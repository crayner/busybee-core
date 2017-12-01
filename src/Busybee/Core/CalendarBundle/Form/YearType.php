<?php

namespace Busybee\Core\CalendarBundle\Form;

use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\Core\CalendarBundle\Events\YearSubscriber;
use Busybee\Core\CalendarBundle\Model\YearManager;
use Busybee\Core\CalendarBundle\Validator\CalendarGroup;
use Busybee\Core\CalendarBundle\Validator\Grade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Busybee\Core\CalendarBundle\Validator\CalendarStatus;
use Busybee\Core\CalendarBundle\Validator\CalendarDate;
use Busybee\Core\CalendarBundle\Validator\TermDate;
use Busybee\Core\CalendarBundle\Validator\SpecialDayDate;

class YearType extends AbstractType
{
	/**
	 * @var array
	 */
	private $statusList;

	/**
	 * @var YearManager
	 */
	private $manager;

	public function __construct($list, YearManager $manager)
	{
		$this->statusList = $list;
		$this->manager    = $manager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', null,
				array(
					'label' => 'calendar.label.name',
					'attr'  => array(
						'help' => 'calendar.help.name',
					),
				)
			)
			->add('firstDay', null,
				array(
					'label' => 'calendar.label.firstDay',
					'attr'  => array(
						'help' => 'calendar.help.firstDay',
					),
				)
			)
			->add('lastDay', null,
				array(
					'label'       => 'calendar.label.lastDay',
					'attr'        => array(
						'help' => 'calendar.help.lastDay',
					),
					'constraints' => array(
						new CalendarDate(array('fields' => $options['data'])),
					),
				)
			)
			->add('status', ChoiceType::class,
				array(
					'label'       => 'calendar.label.status',
					'attr'        => array(
						'help' => 'calendar.help.status',
					),
					'choices'     => $this->statusList,
					'placeholder' => 'calendar.placeholder.status',
					'constraints' => array(
						new CalendarStatus(array('id' => is_null($options['data']->getId()) ? 'Add' : $options['data']->getId())),
					),
				)
			)
			->add('terms', CollectionType::class, array(
					'entry_type'    => TermType::class,
					'allow_add'     => true,
					'entry_options' => array(
						'year_data' => $options['data'],
					),
					'constraints'   => array(
						new TermDate($options['data']),
					),
					'label'         => false,
					'attr'          => array(
						'class' => 'termList'
					),
					'by_reference'  => false,
				)
			)
			->add('specialDays', CollectionType::class, array(
					'entry_type'    => SpecialDayType::class,
					'allow_add'     => true,
					'entry_options' => array(
						'year_data' => $options['data'],
					),
					'constraints'   => array(
						new SpecialDayDate($options['data']),
					),
					'label'         => false,
					'allow_delete'  => true,
					'attr'          => array(
						'class' => 'specialDayList'
					),
					'by_reference'  => false,
				)
			)
			->add('calendarGroups', CollectionType::class, array(
					'entry_type'    => CalendarGroupType::class,
					'allow_add'     => true,
					'entry_options' => array(
						'year_data' => $options['data'],
						'manager'   => $options['calendarGroupManager'],
					),
					'constraints'   => [
						new CalendarGroup($options['data']),
					],
					'label'         => false,
					'allow_delete'  => true,
					'attr'          => array(
						'class' => 'calendarGroupList'
					),
					'by_reference'  => false,
				)
			)
			->add('downloadCache', HiddenType::class);

		$builder->addEventSubscriber(new YearSubscriber($this->manager));
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class'         => Year::class,
				'translation_domain' => 'BusybeeCalendarBundle',
			)
		);
		$resolver->setRequired(
			[
				'calendarGroupManager',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'calendar_year';
	}


}
