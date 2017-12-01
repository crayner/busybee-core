<?php
namespace Busybee\People\StudentBundle\Form;

use Busybee\Core\CalendarBundle\Entity\CalendarGroup;
use Busybee\Core\TemplateBundle\Type\EntityType;
use Busybee\Core\TemplateBundle\Type\SettingChoiceType;
use Busybee\People\StudentBundle\Entity\StudentCalendarGroup;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentCalendarGroupType extends AbstractType
{
	/**
	 * @var ObjectManager
	 */
	private $om;

	/**
	 * StaffType constructor.
	 *
	 * @param ObjectManager $om
	 */
	public function __construct(ObjectManager $om)
	{
		$this->om = $om;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('status', SettingChoiceType::class,
				[
					'setting_name' => 'student.enrolment.status',
					'label'        => 'calendar.groups.label.status',
					'placeholder'  => 'calendar.groups..placeholder.status',
					'attr'         => [
						'help' => 'calendar.groups..help.status',
					],
				]
			)
			->add('student', HiddenType::class)
			->add('calendarGroup', EntityType::class,
				[
					'class'         => CalendarGroup::class,
					'choice_label'  => 'fullName',
					'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('g')
							->leftJoin('g.year', 'y')
							->orderBy('y.firstDay', 'DESC')
							->addOrderBy('g.sequence', 'ASC');
					},
					'placeholder'   => 'student.calendar.group.placeholder',
					'label'         => 'student.calendar.group.labele',
					'attr'          => [
						'help' => 'student.calendar.group.help',
					],
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
					'data_class'         => StudentCalendarGroup::class,
					'translation_domain' => 'BusybeeStudentBundle',
					'systemYear'         => null,
					'error_bubbling'     => true,
				]
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'calendar_group_by_student';
	}


}
