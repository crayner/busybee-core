<?php

namespace Busybee\Core\TemplateBundle\Type;

use Busybee\Core\TemplateBundle\Form\DataTransformer\YamlToStringTransformer;
use Busybee\Core\TemplateBundle\Model\FileUpLoad;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class YamlType extends AbstractType
{
	private $loader;

	public function __construct(FileUpLoad $loader)
	{
		$this->loader = $loader;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'compound' => false,
				'multiple' => false,
				'type'     => 'file',
			)
		);
	}

	public function getBlockPrefix()
	{
		return 'yaml';
	}

	public function getParent()
	{
		return FileType::class;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->addModelTransformer(new YamlToStringTransformer($this->loader));
	}
}