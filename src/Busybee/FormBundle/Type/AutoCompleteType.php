<?php
namespace Busybee\FormBundle\Type ;

use Symfony\Component\Form\AbstractType ;
use Symfony\Component\OptionsResolver\OptionsResolver ;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ;
use Symfony\Component\Form\FormView ;
use Symfony\Component\Form\FormInterface ;

class AutoCompleteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
			array(
				'mapped'	=> false,
				'hidden'	=> array(),
			)
		)
		->setRequired(
			array(
				'hidden',
			)
		);
    }

    public function getBlockPrefix()
    {
        return 'auto_complete';
    }

    public function getParent()
    {
        return EntityType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars = array_merge($view->vars, 
			array(
				'hidden' => $options['hidden']
			)
		);
    }

}