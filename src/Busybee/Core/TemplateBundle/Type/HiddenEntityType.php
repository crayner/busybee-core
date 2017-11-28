<?php

namespace Busybee\Core\TemplateBundle\Type;

use Busybee\Core\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HiddenEntityType extends AbstractType
{
	/**
	 * @var ObjectManager
	 */
	private $manager;

	/**
	 * StaffType constructor.
	 *
	 * @param ObjectManager $manager
	 */
	public function __construct(ObjectManager $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->addModelTransformer(new EntityToStringTransformer($this->manager, $options));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'bee_entity_hidden';
	}

	public function getParent()
	{
		return HiddenType::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setRequired(
			[
				'class',
			]
		);
		$resolver->setDefaults(
			[
				'multiple' => false,
			]
		);
	}
}