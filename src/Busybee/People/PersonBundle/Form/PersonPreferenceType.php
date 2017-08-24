<?php

namespace Busybee\People\PersonBundle\Form;

use Busybee\Core\FormBundle\Type\SettingChoiceType;
use Busybee\Core\FormBundle\Type\TextType;
use Busybee\People\PersonBundle\Entity\Person;
use Busybee\People\PersonBundle\Entity\PersonPreference;
use Busybee\Core\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonPreferenceType extends AbstractType
{
	/**
	 * @var ObjectManager
	 */
	private $om;

	/**
	 * PersonPreferenceType constructor.
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
		$locale     = $options['locale'];
		$localeList = $options['localeList'];
		$builder
			->add('language', ChoiceType::class,
				[
					'label'                     => 'person.preference.language.label',
					'required'                  => false,
					'placeholder'               => 'person.preference.language.placeholder',
					'mapped'                    => false,
					'choice_translation_domain' => false,
					'choices'                   => $localeList,
				]
			);;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class'         => PersonPreference::class,
			'translation_domain' => 'BusybeePersonBundle',
			'allow_extra_fields' => true,
		));
		$resolver->setRequired(
			[
				'locale',
				'localeList',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'person_preference';
	}


}
