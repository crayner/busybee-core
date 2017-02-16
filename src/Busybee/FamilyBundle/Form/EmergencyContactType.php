<?php

namespace Busybee\FamilyBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

class EmergencyContactType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'emergencyContact';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'emergencyContact';
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return EntityType::class;
    }
}
