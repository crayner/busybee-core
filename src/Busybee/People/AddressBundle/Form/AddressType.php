<?php

namespace Busybee\People\AddressBundle\Form;

use Busybee\Core\TemplateBundle\Type\AutoCompleteType;
use Busybee\Core\TemplateBundle\Type\SettingChoiceType;
use Busybee\People\AddressBundle\Entity\Address;
use Busybee\People\LocalityBundle\Entity\Locality;
use Busybee\People\AddressBundle\Events\AddressSubscriber;
use Busybee\Core\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\Core\SecurityBundle\Form\ResetType;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Busybee\Core\SystemBundle\Setting\SettingManager;

class AddressType extends AbstractType
{
	/**
	 * @var    SettingManager
	 */
	private $sm;
	/**
	 * @var    ObjectManager
	 */
	private $em;

	/**
	 * Construct
	 */
	public function __construct(SettingManager $sm, ObjectManager $em)
	{
		$this->sm = $sm;
		$this->lr = $em->getRepository(Locality::class);
		$this->em = $em;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('buildingType', SettingChoiceType::class,
				array(
					'label'        => 'address.buildingType.label',
					'attr'         => array(
						'help'  => 'address.buildingType.help',
						'class' => 'beeBuildingType monitorChange',
					),
					'setting_name' => 'Address.BuildingType',
					'required'     => false,
				)
			)
			->add('buildingNumber', null, array(
					'label'    => 'address.buildingNumber.label',
					'attr'     => array(
						'help'      => 'address.buildingNumber.help',
						'maxLength' => 10,
						'class'     => 'beeBuildingNumber monitorChange',
					),
					'required' => false,
				)
			)
			->add('streetNumber', null, array(
					'label'    => 'address.streetNumber.label',
					'attr'     => array(
						'help'      => 'address.streetNumber.help',
						'maxLength' => 10,
						'class'     => 'beeStreetNumber monitorChange',
					),
					'required' => false,
				)
			)
			->add('propertyName', null, array(
					'label'    => 'address.propertyName.label',
					'attr'     => array(
						'help'  => 'address.propertyName.help',
						'class' => 'beePropertyName monitorChange',
					),
					'required' => false,
				)
			)
			->add('streetName', null, array(
					'label' => 'address.streetName.label',
					'attr'  => array(
						'help'  => 'address.streetName.help',
						'class' => 'beeStreetName monitorChange',
					),
				)
			)
			->add('locality', EntityType::class,
				array(
					'class'         => Locality::class,
					'label'         => 'address.locality.label',
					'choice_label'  => 'fullLocality',
					'placeholder'   => 'address.locality.placeholder',
					'attr'          => array(
						'help'  => 'address.locality.help',
						'class' => 'beeLocality monitorChange',
					),
					'query_builder' => function (EntityRepository $lr) {
						return $lr->createQueryBuilder('l')
							->orderBy('l.name', 'ASC')
							->addOrderBy('l.postCode', 'ASC');
					},
				)
			)
			->add('addressList', AutoCompleteType::class,
				array(
					'class'        => Address::class,
					'label'        => 'address.addressList.label',
					'choice_label' => 'singleLineAddress',
					'empty_data'   => null,
					'required'     => false,
					'attr'         => array(
						'help'  => 'address.addressList.help',
						'class' => 'beeAddressList formChanged',
					),
					'mapped'       => false,
				)
			);

		$builder->get('locality')->addModelTransformer(new EntityToStringTransformer($this->em, Locality::class));
		$builder->addEventSubscriber(new AddressSubscriber());

	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class'         => Address::class,
				'translation_domain' => 'BusybeeAddressBundle',
				'classSuffix'        => null,
				'allow_extra_fields' => true,
			)
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
		return 'address';
	}


}
