<?php
namespace Busybee\FormBundle\Type ;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface ;
use Busybee\FormBundle\Form\DataTransformer\ImageToStringTransformer ;
use Busybee\FormBundle\Model\ImageUploader ;
use Symfony\Component\Form\Extension\Core\Type\FileType ;

class ImageType extends AbstractType
{
    private $uploader;
	
	public function __construct( ImageUploader $uploader)
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
			)
		);
    }

    public function getBlockPrefix()
    {
        return 'image';
    }

    public function getParent()
    {
        return FileType::class ;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ImageToStringTransformer($this->uploader));
    }
}