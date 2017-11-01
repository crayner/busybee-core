<?php
namespace Busybee\People\FamilyBundle\Form;

use Busybee\People\FamilyBundle\Entity\Family;
use Busybee\People\FamilyBundle\Events\FamilySubscriber;
use Busybee\People\FamilyBundle\Model\FamilyManager;
use Busybee\Core\TemplateBundle\Type\AutoCompleteType;
use Busybee\Core\TemplateBundle\Type\SettingChoiceType;
use Busybee\People\AddressBundle\Entity\Address;
use Busybee\People\PhoneBundle\Form\PhoneType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FamilyType extends AbstractType
{
	/**
	 * @var FamilyManager
	 */
	private $familyManager;

	public function __construct(FamilyManager $familyManager)
	{
		$this->familyManager = $familyManager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', null, array(
					'label'    => 'family.label.name',
					'required' => false,
					'attr'     => array(
						'help' => 'family.help.name'
					),
				)
			)
			->add('address1', AutoCompleteType::class,
				array(
					'class'              => Address::class,
					'data'               => $options['data']->getAddress1(),
					'choice_label'       => 'singleLineAddress',
					'empty_data'         => null,
					'required'           => false,
					'label'              => 'person.address1.label',
					'attr'               => array(
						'help'  => 'person.address1.help',
						'class' => 'beeAddressList formChanged',
					),
					'translation_domain' => 'BusybeePersonBundle',
				)
			)
			->add('address2', AutoCompleteType::class,
				array(
					'class'              => Address::class,
					'choice_label'       => 'singleLineAddress',
					'data'               => $options['data']->getAddress2(),
					'empty_data'         => null,
					'required'           => false,
					'label'              => 'person.address2.label',
					'attr'               => array(
						'help'  => 'person.address2.help',
						'class' => 'beeAddressList formChanged',
					),
					'translation_domain' => 'BusybeePersonBundle',
				)
			)
			->add('phone', CollectionType::class, array(
					'label'              => 'person.phones.label',
					'entry_type'         => PhoneType::class,
					'allow_add'          => true,
					'by_reference'       => false,
					'allow_delete'       => true,
					'attr'               => array(
						'class' => 'phoneNumberList'
					),
					'translation_domain' => 'BusybeePersonBundle',
					'required'           => false,
				)
			)
			->add('careGivers', CollectionType::class, array(
					'label'        => 'family.caregivers.label',
					'entry_type'   => CareGiverType::class,
					'allow_add'    => true,
					'by_reference' => false,
					'allow_delete' => true,
					'attr'         => array(
						'class' => 'careGiverList',
						'help'  => 'family.caregivers.help',
					),
					'required'     => false,
				)
			)
			->add('students', CollectionType::class, array(
					'label'        => 'family.label.students',
					'entry_type'   => StudentType::class,
					'allow_add'    => true,
					'by_reference' => false,
					'allow_delete' => true,
					'attr'         => array(
						'class' => 'studentList',
						'help'  => 'family.help.students',
					),
					'required'     => false,
				)
			)
			->add('firstLanguage', LanguageType::class, array(
					'label'       => 'family.label.language.first',
					'placeholder' => 'family.placeholder.language',
					'required'    => false,
				)
			)
			->add('secondLanguage', LanguageType::class, array(
					'label'       => 'family.label.language.second',
					'placeholder' => 'family.placeholder.language',
					'required'    => false,
				)
			)
			->add('house', SettingChoiceType::class, array(
					'label'        => 'family.label.house',
					'placeholder'  => 'family.placeholder.house',
					'required'     => false,
					'attr'         => array(
						'help' => 'family.help.house',
					),
					'setting_name' => 'house.list',
				)
			);
		$builder->addEventSubscriber(new FamilySubscriber($this->familyManager));

	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class'         => Family::class,
			'translation_domain' => 'BusybeeFamilyBundle',
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'family';
	}


}