<?php

namespace General\ValidationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver ;

class MessageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('message_key', 'choice', array(
                    'label'					=> 'message.label.key',
                    'choices'            	=> array(
                        'default'            	=> 'Default',
                        'notblank'            	=> 'Not Blank',
                        'unique'            	=> 'Unique',
                        'Match'               	=> 'Match',
                        'minlength'            	=> 'Minimum Length',
                        'length'            	=> 'Maximum Length',
                        'enumeration'        	=> 'Enumeration',
                    ),
                )
            )
            ->add('message_value', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
	                    'label'                	=> 'message.label.value',
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
                'data_class'             	=> null,
                'translation_domain'     	=> 'GeneralValidationBundle',
                'validation_groups'        	=> null,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'message';
    }
}
