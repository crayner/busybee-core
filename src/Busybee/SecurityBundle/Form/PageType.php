<?php

namespace Busybee\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('route', null,
                array(
                    'label' => 'security.page.label.route',
                    'attr' => array(
                        'readOnly' => 'readOnly',
                    )
                )
            )
            ->add('path', null,
                array(
                    'label' => 'security.page.label.path',
                    'attr' => array(
                        'readOnly' => 'readOnly',
                    )
                )
            )
            ->add('roles', DirectRoleType::class, array(
                    'label'                 => 'security.page.label.roles',
                    'multiple' 				=> true,
                    'expanded' 				=> true,
                    'attr'					=> array(
                        'help' 					=> 'security.page.help.roles',
                    ),
                    'required' => true,
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
            'data_class' => 'Busybee\SecurityBundle\Entity\Page',
            'translation_domain' => 'BusybeeSecurityBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'busybee_securitybundle_page';
    }


}
