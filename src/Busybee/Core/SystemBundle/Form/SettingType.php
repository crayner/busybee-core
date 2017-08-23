<?php

namespace Busybee\Core\SystemBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Busybee\Core\SystemBundle\Repository\SettingRepository;

class SettingType extends AbstractType
{
	private $repo;

	public function __construct(SettingRepository $repo)
	{
		$this->repo = $repo;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('type', HiddenType::class)
			->add('name', HiddenType::class)
			->add('nameSelect', ChoiceType::class,
				array(
					'label'       => '',
					'placeholder' => 'system.setting.placeholder.name',
					'choices'     => $this->getSettingNameChoices(),
					'attr'        => array(
						'class' => 'changeRecord',
					),
					'mapped'      => false,
					'data'        => $options['data']->getNameSelect(),
				)
			)
			->add('displayName', null,
				array(
					'label' => 'system.setting.label.displayName',
					'attr'  => array(
						'help'  => 'system.setting.help.displayName',
						'class' => 'changeSetting',
					)
				)
			)
			->add('description', TextareaType::class,
				array(
					'label' => 'system.setting.label.description',
					'attr'  => array(
						'help'  => 'system.setting.help.description',
						'rows'  => '5',
						'class' => 'changeSetting',
					)
				)
			);
	}

	private function getSettingNameChoices()
	{
		$names    = array();
		$settings = $this->repo->findBy(array(), array('name' => 'ASC'));
		foreach ($settings as $setting)
			$names[$setting->getName()] = $setting->getId();

		return $names;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class'         => 'Busybee\Core\SystemBundle\Entity\Setting',
				'translation_domain' => 'SystemBundle',
				'validation_groups'  => array('Default'),
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'setting';
	}
}
