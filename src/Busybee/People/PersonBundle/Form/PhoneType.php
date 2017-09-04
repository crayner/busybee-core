<?php

namespace Busybee\People\PersonBundle\Form;

use Busybee\Core\TemplateBundle\Type\SettingChoiceType;
use Busybee\People\PersonBundle\Entity\Phone;
use Busybee\People\PersonBundle\Events\PhoneSubscriber;
use Busybee\People\PersonBundle\Repository\PhoneRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Busybee\People\PersonBundle\Form\DataTransformer\PhoneTransformer;

class PhoneType extends AbstractType
{
	/**
	 * @var SettingManager
	 */
	private $sm;

	/**
	 * @var PhoneRepository
	 */
	private $pr;


	/**
	 * Construct
	 */
	public function __construct(SettingManager $sm, PhoneRepository $pr)
	{
		$this->sm = $sm;
		$this->pr = $pr;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('phoneType', SettingChoiceType::class,
				array(
					'label'        => 'person.label.phone.type',
					'setting_name' => 'Phone.TypeList',
				)
			)
			->add('phoneNumber', null,
				array(
					'label' => 'person.label.phone.number',
					'attr'  => array(
						'help' => 'person.help.phone.number',
					),
				)
			)
			->add('countryCode', SettingChoiceType::class,
				array(
					'label'        => 'person.label.phone.country',
					'required'     => false,
					'setting_name' => 'Phone.CountryList',
				)
			);
		$builder->get('phoneNumber')
			->addModelTransformer(new PhoneTransformer());
		$builder->addEventSubscriber(new PhoneSubscriber($this->pr));
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class'         => Phone::class,
				'translation_domain' => 'BusybeePersonBundle',
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'phone';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'phone';
	}


}
