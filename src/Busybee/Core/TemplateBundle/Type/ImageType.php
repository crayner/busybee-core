<?php
namespace Busybee\Core\TemplateBundle\Type;

use Busybee\Core\TemplateBundle\Events\ImageSubscriber;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Busybee\Core\TemplateBundle\Form\DataTransformer\ImageToStringTransformer;
use Busybee\Core\TemplateBundle\Model\ImageUploader;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ImageType extends AbstractType
{
	/**
	 * @var ImageUploader
	 */
	private $uploader;

	/**
	 * ImageType constructor.
	 *
	 * @param ImageUploader $uploader
	 */
	public function __construct(ImageUploader $uploader)
	{
		$this->uploader = $uploader;
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
		$builder->addModelTransformer(new ImageToStringTransformer($this->uploader));
		$builder->addEventSubscriber(new ImageSubscriber());
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