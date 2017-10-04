<?php

namespace Busybee\People\PersonBundle\Form;

use Busybee\People\PersonBundle\Model\ImportManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;


class MatchImportType extends AbstractType
{

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('file', HiddenType::class,
				array(
					'data' => $options['manager']->getFile(),
				)
			)
			->add('fields', CollectionType::class,
				array(
					'entry_type'    => FieldMatchType::class,
					'entry_options' =>
						[
							'manager' => $options['manager'],
						],
					'allow_delete'  => true,
					'allow_add'     => true,
					'attr'          =>
						[
							'class' => 'fieldList',
						],
				)
			)
			->add('offset', HiddenType::class)
			->setAction($options['action']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class'         => ImportManager::class,
				'translation_domain' => 'BusybeePersonBundle',
			)
		);
		$resolver->setRequired(
			[
				'manager',
				'action',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'match_import';
	}

	/**
	 * @param FormView      $view
	 * @param FormInterface $form
	 * @param array         $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['manager'] = $options['manager'];
	}
}
