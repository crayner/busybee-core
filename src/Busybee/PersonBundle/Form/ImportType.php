<?php

namespace Busybee\PersonBundle\Form;

use Busybee\FormBundle\Type\CSVType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ImportType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('importFile', CSVType::class,
				array(
					'label' => 'people.import.label.importFile',
                    'mapped' => false,
                )
			)
            ->add('save', SubmitType::class, array(
                    'label'					=> 'form.button.save',
                    'attr' 					=> array(
                        'class' 				=> 'beeLocalitySave btn btn-success glyphicons glyphicons-plus-sign',
                    ),
                    'translation_domain' => 'BusybeeHomeBundle',
                )
            )
            ->add('return', ButtonType::class,
                array(
                    'label'					=> 'form.button.return',
                    'translation_domain' 	=> 'BusybeeHomeBundle',
                    'attr' 					=> array(
                        'formnovalidate' 		=> 'formnovalidate',
                        'class' 				=> 'btn btn-info glyphicons glyphicons-hand-left',
                        'onClick'				=> "location.href='".$options['data']->returnURL."'",
                    ),
                )
            )
            ->setAction($options['data']->action)
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
			array(
				'data_class' => null,
				'translation_domain' => 'BusybeePersonBundle',
			)
		);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'import';
    }


}
