<?php

namespace Busybee\FormBundle\Type ;

use Busybee\FormBundle\Form\DataTransformer\YesNoTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver ;
use Symfony\Component\Form\FormInterface ;
use Symfony\Component\Form\FormView ;

class YesNoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $emptyData = function (FormInterface $form, $viewData) {
            return $viewData;
        };

        $resolver->setDefaults(
            array(
                'value' 				=> '1',
                'empty_data' 			=> $emptyData,
                'compound' 				=> false,
                'required' 				=> false,
            )
        );
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\CheckboxType';
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'yesno';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new YesNoTransformer();
        $builder->addModelTransformer($transformer);
    }
}