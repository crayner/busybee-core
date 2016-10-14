<?php

namespace Busybee\DatabaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnumeratorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'enumerator.label.name',
				)
			)
            ->add('prompt', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'enumerator.label.prompt',
				)
			)
            ->add('value', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'enumerator.label.value',
				)
			)
            ->add('save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label' 				=> 'form.save',
					'translation_domain' 	=> 'BusybeeDisplayBundle',
					'attr' 					=> array(
						'class' 				=> 'btn btn-success glyphicon glyphicon-save'
					),
				)
			)
            ->add('save_and_add', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label' 				=> 'form.save_and_add',
					'translation_domain' 	=> 'BusybeeDisplayBundle',
					'attr' 					=> array(
						'class' 				=> 'btn btn-success glyphicon glyphicon-plus-sign'
					),
				)
			)
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            	'data_class' 			=> 'Busybee\DatabaseBundle\Entity\Enumerator',
				'translation_domain' 	=> 'BusybeeDatabaseBundle',
        	)
		);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'database_enumerator';
    }
}
