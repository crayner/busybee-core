<?php

namespace Busybee\People\LocalityBundle\Form;

use Busybee\Core\TemplateBundle\Type\SettingChoiceType;
use Busybee\Core\TemplateBundle\Type\TextType;
use Busybee\People\LocalityBundle\Entity\Locality;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class LocalityType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, array(
					'label' => 'locality.name.label',
					'attr'  => array(
						'class' => 'beeLocality monitorChange',
						'help'  => 'locality.name.help',
					),
				)
			)
			->add('territory', SettingChoiceType::class, array(
					'label'        => 'locality.territory.label',
					'attr'         => array(
						'class' => 'beeTerritory monitorChange',
					),
					'setting_name' => 'Address.TerritoryList',
					'placeholder'  => 'locality.territory.placeholder',
				)
			)
			->add('postCode', TextType::class, array(
					'label' => 'locality.postcode.label',
					'attr'  => array(
						'class' => 'beePostCode monitorChange',
					),
				)
			)
			->add('country', CountryType::class, array(
					'label' => 'locality.country.label',
					'attr'  => array(
						'class' => 'beeCountry monitorChange',
					),
				)
			)
			->add('localityList', EntityType::class,
				array(
					'class'         => Locality::class,
					'label'         => 'locality.localityList.label',
					'choice_label'  => 'fullLocality',
					'placeholder'   => 'locality.localityList.placeholder',
					'required'      => false,
					'attr'          => array(
						'help'  => 'locality.localityList.help',
						'class' => 'beeLocalityList formChanged',
					),
					'mapped'        => false,
					'query_builder' => function (EntityRepository $lr) {
						return $lr->createQueryBuilder('l')
							->orderBy('l.name', 'ASC')
							->addOrderBy('l.postCode', 'ASC');
					},
				)
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class'         => Locality::class,
				'translation_domain' => 'BusybeeLocalityBundle',
				'allow_extra_fields' => true,
				'classSuffix'        => null,
				'csrf_protection'    => false,
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'locality';
	}
}
