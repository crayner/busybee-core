<?php

namespace Busybee\People\PersonBundle\Form;

use Busybee\Core\SecurityBundle\Entity\User;
use Busybee\Core\TemplateBundle\Model\PhotoUploader;
use Busybee\Core\TemplateBundle\Model\TabManager;
use Busybee\Core\TemplateBundle\Type\AutoCompleteType;
use Busybee\Core\TemplateBundle\Type\ImageType;
use Busybee\Core\TemplateBundle\Type\SettingChoiceType;
use Busybee\Core\TemplateBundle\Type\ToggleType;
use Busybee\People\AddressBundle\Entity\Address;
use Busybee\People\PersonBundle\Entity\Person;
use Busybee\People\PersonBundle\Events\PersonSubscriber;
use Busybee\People\PersonBundle\Form\Transformer\PersonViewTransformer;
use Busybee\People\PersonBundle\Model\PersonInterface;
use Busybee\People\PersonBundle\Model\PersonManager;
use Busybee\People\PhoneBundle\Form\PhoneType;
use Busybee\People\StaffBundle\Form\StaffType;
use Busybee\People\StudentBundle\Form\StudentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Common\Persistence\ObjectManager;

class PersonType extends AbstractType
{
	/**
	 * @var    PersonManager
	 */
	private $personManager;

	/**
	 * @var    ObjectManager
	 */
	private $manager;

	/**
	 * @var    PhotoUpLoader
	 */
	private $photoLoader;

	/**
	 * @var TabManager
	 */
	private $tm;

	/**
	 * Construct
	 */
	public function __construct(PersonManager $sm, ObjectManager $manager, PhotoUploader $photoLoader, TabManager $tm)
	{
		$this->personManager = $sm;
		$this->manager       = $manager;
		$this->photoLoader   = $photoLoader;
		$this->tm            = $tm;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('title', SettingChoiceType::class, array(
					'label'        => 'person.title.label',
					'setting_name' => 'Person.TitleList',
					'attr'         => array(
						'class' => 'beeTitle',
					),
					'required'     => false,
				)
			)
			->add('identifier', HiddenType::class,
				[
				]
			)
			->add('surname', null, array(
					'label' => 'person.surname.label',
					'attr'  => array(
						'class' => 'beeSurname',
					),
				)
			)
			->add('firstName', null, array(
					'label' => 'person.firstName.label',
					'attr'  => array(
						'class' => 'beeFirstName',
					),
				)
			)
			->add('preferredName', null, array(
					'label'    => 'person.preferredName.label',
					'attr'     => array(
						'class' => 'beePreferredName',
					),
					'required' => false,
				)
			)
			->add('officialName', null, array(
					'label' => 'person.officialName.label',
					'attr'  => array(
						'help'  => 'person.officialName.help',
						'class' => 'beeOfficialName',
					),
				)
			)
			->add('gender', SettingChoiceType::class, array(
					'setting_name'              => 'Person.GenderList',
					'label'                     => 'person.gender.label',
					'attr'                      => array(
						'class' => 'beeGender',
					),
					'choice_translation_domain' => 'BusybeePersonBundle',
				)
			)
			->add('dob', BirthdayType::class, array(
					'label'    => 'person.dob.label',
					'required' => false,
					'attr'     => array(
						'class' => 'beeDob',
					),
				)
			)
			->add('email', EmailType::class, array(
					'label'    => 'person.email.label',
					'required' => false,
					'attr'     => array(
						'help' => 'person.email.help',
					),
				)
			)
			->add('email2', EmailType::class, array(
					'label'    => 'person.email2.label',
					'required' => false,
				)
			)
			->add('photo', ImageType::class, array(
					'attr'        => array(
						'help'       => 'person.photo.help',
						'imageClass' => 'headShot75',
					),
					'label'       => 'person.photo.label',
					'required'    => false,
					'deletePhoto' => $options['deletePhoto'],
				)
			)
			->add('website', UrlType::class, array(
					'label'    => 'person.website.label',
					'required' => false,
				)
			)
			->add('address1', AutoCompleteType::class,
				array(
					'class'        => Address::class,
//					'data'         => $options['data']->getAddress1(),
					'choice_label' => 'singleLineAddress',
					'empty_data'   => null,
					'required'     => false,
					'label'        => 'person.address1.label',
					'attr'         => array(
						'help'  => 'person.address1.help',
						'class' => 'beeAddressList formChanged',
					),
				)
			)
			->add('address2', AutoCompleteType::class,
				array(
					'class'        => Address::class,
					'choice_label' => 'singleLineAddress',
//					'data'         => $options['data']->getAddress2(),
					'empty_data'   => null,
					'required'     => false,
					'label'        => 'person.address2.label',
					'attr'         => array(
						'help'  => 'person.address2.help',
						'class' => 'beeAddressList formChanged',
					),
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
			);
		$builder->addEventSubscriber(new PersonSubscriber($this->personManager, $this->manager, $this->tm, $options['isSystemAdmin']));
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class'         => Person::class,
				'translation_domain' => 'BusybeePersonBundle',
				'allow_extra_fields' => true,
			)
		);
		$resolver->setRequired(
			[
				'deletePhoto',
				'isSystemAdmin',
				'systemYear',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'person';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getYears()
	{
		$years = array();
		for ($i = -100; $i <= 0; $i++)
		{
			$years[] = date('Y', strtotime($i . ' Years'));
		}

		return $years;
	}
}
