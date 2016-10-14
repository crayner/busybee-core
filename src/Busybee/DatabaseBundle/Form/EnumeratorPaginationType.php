<?php

namespace Busybee\DatabaseBundle\Form;

use Busybee\PaginationBundle\Form\PaginationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Busybee\DatabaseBundle\Entity\TableRepository;

class EnumeratorPaginationType extends PaginationType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder = $this->addHidden($builder, $options);
        $builder = $this->addLimit($builder, $options);
        $builder = $this->addSort($builder, $options);
        $builder = $this->addSearch($builder, $options);
    }

}
