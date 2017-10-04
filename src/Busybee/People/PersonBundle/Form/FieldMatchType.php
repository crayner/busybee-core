<?php

namespace Busybee\People\PersonBundle\Form;

use Busybee\People\PersonBundle\Entity\FieldMatch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;


class FieldMatchType extends AbstractType
{

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('source', ChoiceType::class,
				[
					'label'       => 'people.matchimport.source.label',
					'choices'     => array_flip($options['manager']->getHeaderNames()->toArray()),
					'placeholder' => 'people.matchimport.source.placeholder',
				]
			)
			->add('destination', ChoiceType::class,
				[
					'label'       => 'people.matchimport.destination.label',
					'choices'     => array_flip($options['manager']->getFieldNames()),
					'placeholder' => 'people.matchimport.destination.placeholder',
					'attr'        =>
						[
							'class' => 'optionLoader'
						],
				]
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class'         => FieldMatch::class,
				'translation_domain' => 'BusybeePersonBundle',
			]
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
		return 'field_match';
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
