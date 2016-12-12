<?php
namespace Busybee\FormBundle\Type ;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface ;
use Busybee\FormBundle\Form\DataTransformer\TimeToStringTransformer ;
use Busybee\FormBundle\Model\ImageUploader ;
use Symfony\Component\Form\Extension\Core\Type\TimeType as TimeCoreType;

class TimeType extends AbstractType
{
	private $tz;
	
	public function __construct($tz)
	{
		$this->tz = $tz;
	}
   	/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
			array(
				'compound' 				=> true,
				'multiple' 				=> false,
			)
		);
    }

    public function getBlockPrefix()
    {
        return 'bee_time';
    }

    public function getParent()
    {
        return TimeCoreType::class ;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new TimetoStringTransformer($this->tz));
    }
}