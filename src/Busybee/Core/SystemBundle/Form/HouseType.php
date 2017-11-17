<?php

namespace Busybee\Core\SystemBundle\Form;

use Busybee\Core\SystemBundle\Event\HouseSubscriber;
use Busybee\Core\SystemBundle\Model\House;
use Busybee\Core\SystemBundle\Model\HouseManager;
use Busybee\Core\TemplateBundle\Type\ImageType;
use Busybee\Core\TemplateBundle\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class HouseType extends AbstractType
{
	/**
	 * @var HouseManager
	 */
	private $houseManager;

	/**
	 * HouseSubscriber constructor.
	 *
	 * @param HouseManager $houseManager
	 */
	public function __construct(HouseManager $houseManager)
	{
		$this->houseManager = $houseManager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class,
				[
					'label'       => 'school.admin.house.name.label',
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('shortName', TextType::class,
				[
					'label'       => 'school.admin.house.shortname.label',
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('logo', ImageType::class,
				[
					'label'       => 'school.admin.house.logo.label',
					'attr'        => [
						'help'       => 'school.admin.house.logo.help',
						'imageClass' => 'smallLogo',
					],
					'constraints' => [
						new Image(['maxRatio' => 1.25, 'minRatio' => 0.75, 'maxSize' => '750k']),
					],
					'required'    => false,
					'deletePhoto' => $options['deletePhoto'],
					'fileName'    => 'house',
				]
			);
		$builder->addEventSubscriber(new HouseSubscriber($this->houseManager));
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'translation_domain' => 'SystemBundle',
				'data_class'         => House::class,
			]
		);
		$resolver->setRequired(
			[
				'deletePhoto',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'house';
	}

	/**
	 * @param FormView      $view
	 * @param FormInterface $form
	 * @param array         $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['deletePhoto'] = $options['deletePhoto'];
	}
}
