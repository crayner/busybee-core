<?php

namespace Busybee\FormBundle\Type;

use Busybee\FormBundle\Form\DataTransformer\YesNoTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
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
                'div_class' => 'yesno-right'
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

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars,
            array(
                'div_class' => $options['div_class'],
            )
        );
    }
}