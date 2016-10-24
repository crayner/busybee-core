<?php

namespace Busybee\FormBundle\Type ;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType ;
use Symfony\Component\OptionsResolver\OptionsResolver ;
use Symfony\Component\Form\FormInterface ;
use Symfony\Component\Form\FormView ;
use Symfony\Component\Form\AbstractTypeExtension;


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

        $resolver->setDefaults(array(
			'help' 					=> null,
			'attr'					=> array(
				'data-off-label' 		=> "false",
				'data-on-label' 		=> "false",
				'data-off-icon-cls'	 	=> "glyphicons-thumbs-down",
				'data-on-icon-cls' 		=> "glyphicons-thumbs-up",
				'class'					=> 'yes-no',
			),
            'value' 				=> '1',
            'empty_data' 			=> $emptyData,
            'compound' 				=> false,
			'required' 				=> false,
			'data' 					=> false,
        	)
		);
    }

    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\CheckboxType';
    }

    public function getBlockPrefix()
    {
        return 'yesno';
    }

	/**
	 * {@inheritdoc}
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['help'] = $options['help'];
	}
}