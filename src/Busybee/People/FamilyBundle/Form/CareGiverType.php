<?php

namespace Busybee\People\FamilyBundle\Form;

use Busybee\People\FamilyBundle\Entity\CareGiver;
use Busybee\People\FamilyBundle\Entity\Family;
use Busybee\Core\TemplateBundle\Type\SettingChoiceType;
use Busybee\Core\TemplateBundle\Type\ToggleType;
use Busybee\People\PersonBundle\Entity\Person;
use Busybee\Core\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CareGiverType extends AbstractType
{
	/**
	 * @var ObjectManager
	 */
	private $manager;

	/**
	 * CareGiverType constructor.
	 *
	 * @param ObjectManager $manager
	 */
	public function __construct(ObjectManager $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('person', EntityType::class, array(
					'label'         => 'caregiver.label.person',
					'class'         => Person::class,
					'choice_label'  => 'formatName',
					'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('p')
							->where('p.studentQuestion = 0')
							->addOrderBy('p.surname', 'ASC')
							->addOrderBy('p.firstName', 'ASC');
					},
					'placeholder'   => 'caregiver.placeholder.person',
				)
			)
			->add('emailContact', ToggleType::class, array(
					'label'     => 'caregiver.label.emailcontact',
					'attr'      => array(
						'data-size' => 'mini',
					),
					'div_class' => 'toggleLeft',
				)
			)
			->add('smsContact', ToggleType::class, array(
					'label'     => 'caregiver.label.smscontact',
					'attr'      => array(
						'data-size' => 'mini',
					),
					'div_class' => 'toggleLeft',
				)
			)
			->add('mailContact', ToggleType::class, array(
					'label'     => 'caregiver.label.mailcontact',
					'attr'      => array(
						'data-size' => 'mini',
					),
					'div_class' => 'toggleLeft',
				)
			)
			->add('phoneContact', ToggleType::class, array(
					'label'     => 'caregiver.label.phonecontact',
					'attr'      => array(
						'data-size' => 'mini',
					),
					'div_class' => 'toggleLeft',
				)
			)
			->add('family', HiddenType::class)
			->add('contactPriority', HiddenType::class)
			->add('comment', null, array(
					'label' => 'caregiver.label.comment',
				)
			)
			->add('relationship', SettingChoiceType::class, array(
					'label'        => 'caregiver.label.relationship',
					'setting_name' => 'Student.CareGiver.Relationship.List',
				)
			)
			->add('newsletter', ToggleType::class, array(
					'label'     => 'caregiver.label.newsletter',
					'attr'      => array(
						'data-size' => 'mini',
					),
					'div_class' => 'toggleLeft',
				)
			)
			->add('finance', ToggleType::class, array(
					'label'     => 'caregiver.label.finance',
					'attr'      => array(
						'data-size' => 'mini',
					),
					'div_class' => 'toggleLeft',
				)
			)
			->add('pickUpAllowed', ToggleType::class, array(
					'label'     => 'caregiver.label.pickUpAllowed',
					'attr'      => array(
						'data-size' => 'mini',
					),
					'div_class' => 'toggleLeft',
				)
			)
			->add('emergencyOnly', ToggleType::class, array(
					'label'     => 'caregiver.label.emergencyOnly',
					'attr'      => array(
						'data-size' => 'mini',
					),
					'div_class' => 'toggleLeft',
				)
			)
			->add('reporting', ToggleType::class, array(
					'label'     => 'caregiver.label.reporting',
					'attr'      => array(
						'data-size' => 'mini',
					),
					'div_class' => 'toggleLeft',
				)
			);
		$builder->get('family')->addModelTransformer(new EntityToStringTransformer($this->manager, Family::class));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'caregiver';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'caregiver';
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class'         => CareGiver::class,
			'translation_domain' => 'BusybeeFamilyBundle',
			'currentOrder'       => 0,
		));
	}
}