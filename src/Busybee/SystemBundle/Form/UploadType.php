<?php

namespace Busybee\SystemBundle\Form;

use Busybee\FormBundle\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
            ->add('file', FileType::class,
                array(
                    'label' => 'system.setting.label.upload',
                    'mapped' => false,
                )
            )
            ->add('default', ToggleType::class,
                array(
                    'label' => 'system.setting.label.default',
                    'attr' => array(
                        'help' => 'system.setting.help.default',
                    ),
                    'mapped' => false,
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'SystemBundle',
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
