<?php
namespace Busybee\Core\TemplateBundle\Type;

use Busybee\Core\TemplateBundle\Events\ImageSubscriber;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Busybee\Core\TemplateBundle\Form\DataTransformer\ImageToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ImageType extends AbstractType
{
	/**
	 * @var string
	 */
	private $targetDir;

	/**
	 * ImageSubscriber constructor.
	 *
	 * @param string $targetDir
	 */
	public function __construct(string $targetDir)
	{
		$this->targetDir = $targetDir;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'compound'     => false,
				'multiple'     => false,
				'type'         => 'file',
				'deleteTarget' => '_self',
				'deleteParams' => null,
			]
		);

		$resolver->setRequired(
			[
				'deletePhoto',
				'deleteTarget',
				'deleteParams',
				'fileName',
			]
		);
	}

	/**
	 * @return string
	 */
	public function getBlockPrefix()
	{
		return 'image';
	}

	/**
	 * @return mixed
	 */
	public function getParent()
	{
		return FileType::class;
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->addModelTransformer(new ImageToStringTransformer());
		$builder->addEventSubscriber(new ImageSubscriber($this->targetDir));
	}

	/**
	 * @param FormView      $view
	 * @param FormInterface $form
	 * @param array         $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['deletePhoto']  = $options['deletePhoto'];
		$view->vars['deleteTarget'] = $options['deleteTarget'];
		$view->vars['deleteParams'] = $options['deleteParams'];
	}
}