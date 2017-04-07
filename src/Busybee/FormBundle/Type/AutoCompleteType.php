<?php
namespace Busybee\FormBundle\Type ;

use Symfony\Component\Form\AbstractType ;
use Symfony\Component\OptionsResolver\OptionsResolver ;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ;


class AutoCompleteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }

    public function getBlockPrefix()
    {
        return 'auto_complete';
    }

    public function getParent()
    {
        return EntityType::class;
    }
}