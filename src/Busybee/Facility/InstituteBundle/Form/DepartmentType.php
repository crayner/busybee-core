<?php

namespace Busybee\Facility\InstituteBundle\Form;

use Busybee\Core\TemplateBundle\Type\ImageType;
use Busybee\Core\TemplateBundle\Type\SettingChoiceType;
use Busybee\Core\TemplateBundle\Validator\Constraints\LogoValidator;
use Busybee\Facility\InstituteBundle\Entity\Department;
use Busybee\Facility\InstituteBundle\Events\DepartmentSubscriber;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartmentType extends AbstractType
{
	/**
	 * @var SettingManager
	 */
	private $sm;

	/**
	 * @var ObjectManager
	 */
	private $om;

	/**
	 * DepartmentType constructor.
	 *
	 * @param SettingManager $om
	 */
	public function __construct(SettingManager $sm, ObjectManager $om)
	{
		$this->sm = $sm;
		$this->om = $om;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', null,
				[
					'label' => 'department.name.label'
				]
			)
			->add('type', SettingChoiceType::class,
				[
					'label'        => 'department.type.label',
					'setting_name' => 'department.type.list',
					'placeholder'  => 'department.type.placeholder',
				]
			)
			->add('nameShort', null,
				[
					'label' => 'department.nameShort.label'
				]
			)
			->add('departmentList', EntityType::class, array(
					'class'         => Department::class,
					'attr'          => array(
						'class' => 'departmentList changeRecord formChanged',
					),
					'label'         => '',
					'mapped'        => false,
					'choice_label'  => 'name',
					'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('d')
							->orderBy('d.name', 'ASC');
					},
					'placeholder'   => 'department.departments.placeholder',
					'required'      => false,
					'data'          => $options['data']->getId(),
				)
			)
			->add('logo', ImageType::class,
				[
					'label'       => 'department.logo.label',
					'required'    => false,
					'attr'        => [
						'imageClass' => 'smallLogo'
					],
					'deletePhoto' => $options['deletePhoto'],
				]
			)
			->add('blurb', CKEditorType::class,
				[
					'label'    => 'department.blurb.label',
					'attr'     => [
						'rows' => 4,
					],
					'required' => false,

				]
			)
			->add('importIdentifier', HiddenType::class);

		$builder->addEventSubscriber(new DepartmentSubscriber($this->sm, $this->om));
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class'         => Department::class,
				'translation_domain' => 'BusybeeInstituteBundle',
				'deletePhoto'        => null,
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'department';
	}


}
