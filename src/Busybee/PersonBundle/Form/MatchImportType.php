<?php

namespace Busybee\PersonBundle\Form;

use Busybee\FormBundle\Type\CSVType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class MatchImportType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = array();
        $data['headerNames'] = $options['data']['headerNames'];
        $data['destinationNames'] = $options['data']['destinationNames'];
        $builder
			->add('file', HiddenType::class,
				array(
					'data' => $options['data']['file'],
                    'mapped' => false,
                )
			)
            ->add('fields', CollectionType::class,
                array(
                    'entry_type' => FieldMatchType::class,
                    'entry_options' => array(
                        'data' => $data,
                    ),
                )
            )
            ->add('offset', HiddenType::class,
                array(
                    'data'      => '0',
                    'mapped'    => false,
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
                        'onClick'				=> "location.href='".$options['data']['returnURL']."'",
                    ),
                )
            )
            ->setAction($options['data']['action'])
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
