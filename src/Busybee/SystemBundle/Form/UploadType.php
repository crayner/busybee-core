<?php

namespace Busybee\SystemBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UploadType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('file', 'Symfony\Component\Form\Extension\Core\Type\FileType',
				array (
					'label'		=> 'system.setting.label.upload',
					'mapped'	=> false,
				)
			)
		 	->add('upload', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
				'label'					=> 'form.upload',
				'translation_domain' 	=> 'BusybeeHomeBundle',
				'attr' 					=> array(
					'class'					=> 'btn btn-success glyphicons glyphicons-disk-export'
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
			'translation_domain' => 'BusybeeSystemBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'setting_upload';
    }


}
