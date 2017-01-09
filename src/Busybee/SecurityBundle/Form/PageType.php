<?php

namespace Busybee\SecurityBundle\Form;

use Busybee\SecurityBundle\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            ->add('roles', EntityType::class, array(
                    'label'                 => 'security.page.label.roles',
                    'multiple' 				=> true,
                    'expanded' 				=> true,
                    'class' 				=> Role::class,
                    'choice_label' 			=> 'role',
                    'attr'					=> array(
                        'help' 					=> 'security.page.help.roles',
                    ),
                )
            )
            ->add('save', SubmitType::class, array(
                    'label' 				=> 'form.save',
                    'translation_domain' 	=> 'BusybeeHomeBundle',
                    'attr' 					=> array(
                        'class' 				=> 'btn btn-success glyphicons glyphicons-disk-save'
                    ),
                )
            )
            ->add('cancel', ButtonType::class, array(
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
