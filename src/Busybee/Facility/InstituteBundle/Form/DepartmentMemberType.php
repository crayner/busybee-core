<?php
namespace Busybee\Facility\InstituteBundle\Form;

use Busybee\Core\TemplateBundle\Type\SettingChoiceType;
use Busybee\Facility\InstituteBundle\Entity\Department;
use Busybee\Facility\InstituteBundle\Entity\DepartmentMember;
use Busybee\Core\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\People\StaffBundle\Entity\Staff;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartmentMemberType extends AbstractType
{
	/**
	 * @var ObjectManager
	 */
	private $om;

	/**
	 * DepartmentMemberType constructor.
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
		$options['staff_type'] = $options['staff_type'] == 'Learning Area' ? 'Learning' : 'Administration';

		$builder
			->add('staff', EntityType::class,
				[
					'label'         => 'department.members.member.label',
					'class'         => Staff::class,
					'choice_label'  => 'formatName',
					'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('s')
							->orderBy('s.surname', 'ASC')
							->addOrderBy('s.firstName', 'ASC');
					},
					'placeholder'   => 'department.members.member.placeholder',
					'attr'          => [
						'help' => 'department.members.member.help',
					]
				]
			)
			->add('staffType', SettingChoiceType::class,
				[
					'label'        => 'department.members.type.label',
					'setting_name' => 'department.staff.type.list.' . strtolower($options['staff_type']),
					'placeholder'  => 'department.members.type.placeholder',
				]
			)
			->add('department', HiddenType::class);

		$builder->get('department')->addModelTransformer(new EntityToStringTransformer($this->om, Department::class));
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class'         => DepartmentMember::class,
			'translation_domain' => 'BusybeeInstituteBundle',
		));
		$resolver->setRequired(
			[
				'staff_type',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'department_member';
	}
}
