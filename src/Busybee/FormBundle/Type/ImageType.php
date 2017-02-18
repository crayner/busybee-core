<?php
namespace Busybee\FormBundle\Type ;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface ;
use Busybee\FormBundle\Form\DataTransformer\ImageToStringTransformer ;
use Busybee\FormBundle\Model\ImageUploader ;
use Symfony\Component\Form\Extension\Core\Type\FileType ;

class ImageType extends AbstractType
{
    /**
     * @var ImageUploader
     */
    private $uploader;

    /**
     * ImageType constructor.
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
            array(
                'compound' 				=> false,
                'multiple' 				=> false,
                'type'					=> 'file',
                'deletePhoto' => null,
            )
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
        return FileType::class ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ImageToStringTransformer($this->uploader));
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['deletePhoto'] = $options['deletePhoto'];
    }
}