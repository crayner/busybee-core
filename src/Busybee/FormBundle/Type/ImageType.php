<?php

namespace Busybee\FormBundle\Type ;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

class ImageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'compound' => false,
			'multiple' => false,
			'type'	=> 'file',
        ));
    }

    public function getBlockPrefix()
    {
        return 'image';
    }

    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\FileType';
    }
}