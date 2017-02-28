<?php

namespace Busybee\FormBundle\Type;

use Busybee\FormBundle\Form\DataTransformer\YesNoTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;

class ToggleType extends AbstractType
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
                'value' => '1',
                'empty_data' => $emptyData,
                'compound' => false,
                'required' => false,
                'attr' => array(
                    'data-onstyle' => "success",
                    'data-offstyle' => "danger",
                ),
            )
        );
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return CheckboxType::class;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'toggle';
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