<?php

namespace Busybee\People\PhoneBundle\Form;

use Busybee\Core\TemplateBundle\Type\SettingChoiceType;
use Busybee\Core\TemplateBundle\Type\TextType;
use Busybee\People\PhoneBundle\Entity\Phone;
use Busybee\People\PhoneBundle\Events\PhoneSubscriber;
use Busybee\People\PhoneBundle\Repository\PhoneRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Busybee\People\PhoneBundle\Form\DataTransformer\PhoneTransformer;

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
					'label'        => 'phone.type.label',
					'setting_name' => 'phone.typelist',
				)
			)
			->add('phoneNumber', TextType::class,
				array(
					'label' => 'phone.number.label',
					'attr'  => array(
						'help' => 'phone.number.help',
					),
				)
			)
			->add('countryCode', SettingChoiceType::class,
				array(
					'label'        => 'phone.country.label',
					'required'     => false,
					'setting_name' => 'phone.countrylist',
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
				'translation_domain' => 'BusybeePhoneBundle',
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
