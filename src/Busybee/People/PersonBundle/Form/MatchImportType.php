<?php

namespace Busybee\People\PersonBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class MatchImportType extends AbstractType
{

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$data                     = array();
		$data['headerNames']      = $options['data']['headerNames'];
		$data['destinationNames'] = $options['data']['destinationNames'];
		$builder
			->add('file', HiddenType::class,
				array(
					'data'   => $options['data']['file'],
					'mapped' => false,
				)
			)
			->add('fields', CollectionType::class,
				array(
					'entry_type'    => FieldMatchType::class,
					'entry_options' => array(
						'data' => $data,
					),
				)
			)
			->add('offset', HiddenType::class,
				array(
					'data'   => '0',
					'mapped' => false,
				)
			)
			->setAction($options['data']['action']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class'         => null,
				'translation_domain' => 'BusybeePersonBundle',
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'import';
	}


}
