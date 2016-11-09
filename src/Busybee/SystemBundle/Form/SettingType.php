<?php

namespace Busybee\SystemBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SettingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('type', 'Symfony\Component\Form\Extension\Core\Type\HiddenType',
				array (
				)
			)
			->add('name', null, 
				array(
					'label' => 'system.setting.label.name',
					'disabled' => true,
					'attr' => array(
						'help' => 'system.setting.help.name',
					)
				)
			)
			->add('description', TextareaType::class, 
				array(
					'label' => 'system.setting.label.description',
					'attr' => array(
						'help' => 'system.setting.help.description',
						'rows' => '5',
					)
				)
			)
		 	->add('save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
				'label'					=> 'form.save',
				'translation_domain' 	=> 'BusybeeHomeBundle',
				'attr' 					=> array(
					'class'					=> 'btn btn-success glyphicons glyphicons-disk-save'
					),
				)
			)
			->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
					'label'					=> 'form.cancel', 
					'translation_domain' 	=> 'BusybeeHomeBundle',
					'attr' 					=> array(
						'formnovalidate' 		=> 'formnovalidate',
						'class' 				=> 'btn btn-info glyphicons glyphicons-remove-circle',
						'onClick'				=> 'location.href=\''.$options['data']->cancelURL."'",
					),
				)
			)
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Busybee\SystemBundle\Entity\Setting',
			'translation_domain' => 'BusybeeSystemBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'setting';
    }


}
