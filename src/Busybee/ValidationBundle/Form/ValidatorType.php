<?php

namespace General\ValidationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver ;

class ValidatorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $data = $options['data'];
        $main = 
            $builder->create('main_tab', 'tab', array(
                    'label'                    => 'validation.label.tab.main',
                    'mapped'                => false,
                )
            )
        ;

        $main
            ->add('ConstraintName', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                    'label'                    => 'validation.label.name',
                    'data'                    => $data->getConstraintName(),
                )
            )
            ->add('ConstraintGroup', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                    'label'                    => 'validation.label.group',
                    'help_block'            => 'validation.help.group',
                    'data'                    => $data->getConstraintGroup(),
                )
            )
            ->add('notblank', 'checkbox', array(
                    'label'                    => 'validation.label.not_blank',
                    'data'                    => $data->getNotblank(),
                )
            )
            ->add('unique', 'checkbox', array(
                    'label'                    => 'validation.label.unique',
                    'data'                    => $data->getUnique(),
                )
            )
            ->add('match', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                    'label'                    => 'validation.label.match',
                    'help_block'            => 'validation.help.match',
                    'data'                    => $data->getMatch(),
                )
            )
            ->add('replace', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                    'label'                    => 'validation.label.replace',
                    'help_block'            => 'validation.help.replace',
                    'data'                    => $data->getReplace(),
                )
            )

            ->add('format', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                    'label'                    => 'validation.label.format',
                    'help_block'            => 'validation.help.format',
                    'data'                    => $data->getFormat(),
                )
            )

            ->add('length', 'integer', array(
                    'label'                    => 'validation.label.length',
                    'help_block'            => 'validation.help.length',
                    'data'                    => $data->getLength(),
                )
            )
            ->add('minlength', 'integer', array(
                    'label'                    => 'validation.label.minimum',
                    'help_block'            => 'validation.help.minimum',
                    'data'                    => $data->getMinlength(),
                )
            )
            ->add('enumeration', null, array(
                    'label'        			=> 'validation.label.enumeration',
                    'help_block'            => 'validation.help.enumeration',
                )
            )
        ;

        $message = 
            $builder->create('message_tab', 'tab', array(
                    'label'                    => 'validation.label.tab.message',
                    'mapped'                => false,
                )
            )
        ;
        $message
            ->add('message', 'collection', array(
                    'label'                    => 'validation.label.message',
                    'help_block'            => 'validation.help.message',
                    'type'                    => new MessageType(),
                    'data'                    => $data->getMessage(),
                )
            )
        ;
        $enum = 
            $builder->create('enum_tab', 'tab', array(
                    'label'                    => 'validation.label.tab.enumeration',
                    'mapped'                => false,
                )
            )
        ;
        $enum
            ->add('enumeration', 'collection', array(
                    'label'                    => 'validation.label.enumeration',
                    'help_block'            => 'validation.help.enumeration',
                    'data'                    => $data->getEnumeration(),
                )
            )
        ;

        
        $builder
            ->add($main)
            ->add($message)
            ->add($enum)
            ->add('save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                    'label'                 => 'form.save',
                    'translation_domain'     => 'BusybeeDisplayBundle',
                    'attr'                     => array(
                        'class'                 => 'btn btn-success glyphicon glyphicon-save'
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
                'data_class'             => 'General\ValidationBundle\Entity\Validator',
                'translation_domain'     => 'GeneralValidationBundle',
                'validation_groups'        => null,
                'attr'                    => array(
                    'class'                    => 'ajaxForm',
                    'novalidate'            => 'novalidate',
                ),
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'general_validator';
    }
}
