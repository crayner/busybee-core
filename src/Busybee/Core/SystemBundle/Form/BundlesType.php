<?php

namespace Busybee\Core\SystemBundle\Form;

use Busybee\Core\SystemBundle\Model\BundleManager;
use Busybee\Core\TemplateBundle\Type\ToggleType;
use Busybee\Core\TemplateBundle\Type\YamlType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BundlesType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('bundles', CollectionType::class,
				[
					'entry_type'    => BundleType::class,
					'attr'          => [
						'class' => 'bundleCollection',
					],
					'allow_add'     => false,
					'allow_delete'  => false,
					'entry_options' => [
						'bundleList' => $options['data']->getBundleList(),
						'manager'    => $options['data'],
					],
				]
			)
			->add('orgSettingDefault', ToggleType::class,
				[
					'label' => 'bundles.orgSettingDefault.label',
					'attr'  => [
						'help'  => 'bundles.orgSettingDefault.help',
						'class' => 'noSubmit',
					],
				]
			)
			->add('orgSettingFile', YamlType::class,
				[
					'label'    => 'bundles.orgSettingFile.label',
					'attr'     => [
						'help' => [
							'bundles.orgSettingFile.help',
							[
								'%fileName%' => $options['data']->getDefaultFileName(),
							]
						]
					],
					'required' => false,
				]
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'translation_domain' => 'SystemBundle',
			'data_class'         => BundleManager::class,
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'bundles_manage';
	}

	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['manager'] = $options['data'];

	}
}
