<?php

namespace Busybee\Core\CalendarBundle\Events;

use Busybee\Core\CalendarBundle\Entity\Grade;
use Busybee\Core\CalendarBundle\Model\GradeManager;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Busybee\Core\TemplateBundle\Type\EntityType;
use Busybee\Facility\InstituteBundle\Entity\Space;
use Busybee\People\StaffBundle\Entity\Staff;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GradeSubscriber implements EventSubscriberInterface
{
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
	 * @param SettingManager $om
	 */
	public function __construct(SettingManager $settingManager, GradeManager $gradeManager)
	{
		$this->settingManager = $settingManager;
		$this->gradeManager   = $gradeManager;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		// Tells the dispatcher that you want to listen on the form.pre_set_data
		// event and that the preSetData method should be called.
		return array(
			FormEvents::PRE_SUBMIT   => 'preSubmit',
			FormEvents::PRE_SET_DATA => 'preSetData',
		);
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSetData(FormEvent $event)
	{
		$form = $event->getForm();

		$data = $event->getData();

		if ($this->gradeManager->isStaffInstalled())
		{
			$form->add('tutor1', EntityType::class,
				[
					'label'         => 'grade.tutor1.label',
					'class'         => Staff::class,
					'choice_label'  => 'formatName',
					'data_class'    => Staff::class,
					'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('s')
							->orderby('s.surname', 'ASC')
							->addOrderby('s.firstName', 'ASC');
					},
					'required'      => false,
					'placeholder'   => 'grade.tutor.placeholder',
				]
			)
				->add('tutor2', EntityType::class,
					[
						'label'         => 'grade.tutor2.label',
						'class'         => Staff::class,
						'choice_label'  => 'formatName',
						'data_class'    => Staff::class,
						'query_builder' => function (EntityRepository $er) {
							return $er->createQueryBuilder('s')
								->orderby('s.surname', 'ASC')
								->addOrderby('s.firstName', 'ASC');
						},
						'required'      => false,
						'placeholder'   => 'grade.tutor.placeholder',
					]
				)
				->add('tutor3', EntityType::class,
					[
						'label'         => 'grade.tutor3.label',
						'class'         => Staff::class,
						'choice_label'  => 'formatName',
						'data_class'    => Staff::class,
						'query_builder' => function (EntityRepository $er) {
							return $er->createQueryBuilder('s')
								->orderby('s.surname', 'ASC')
								->addOrderby('s.firstName', 'ASC');
						},
						'required'      => false,
						'placeholder'   => 'grade.tutor.placeholder',
					]
				);
		}

		if ($this->gradeManager->isSpaceInstalled())
		{
			$form->add('space', EntityType::class,
				[
					'label'         => 'grade.space.label',
					'class'         => Space::class,
					'choice_label'  => 'name',
					'data_class'    => Space::class,
					'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('s')
							->orderby('s.name', 'ASC');
					},
					'required'      => false,
					'placeholder'   => 'grade.space.placeholder',
				]
			);
		}
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{
		$data = $event->getData();

		if (isset($data['grade']))
		{
			$groups       = $this->settingManager->get('student.groups._flip');
			$data['name'] = $groups[$data['grade']];
		}

		$event->setData($data);
	}
}