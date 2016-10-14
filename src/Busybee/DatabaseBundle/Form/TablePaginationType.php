<?php

namespace Busybee\DatabaseBundle\Form;

use Busybee\PaginationBundle\Form\PaginationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Busybee\DatabaseBundle\Entity\TableRepository;

class TablePaginationType extends PaginationType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder = $this->addLimit($builder, $options);
        $builder = $this->addHidden($builder, $options);
        $builder = $this->addSort($builder, $options);
        $builder = $this->addSearch($builder, $options);
		return $builder;
    }


	/**
	 * @return FormBuilderInterface
	 */
	protected function addSort(FormBuilderInterface $builder, $options) 
	{
		return $builder	
            ->add('current_sort', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
					'data'					=> $options['data']->getSortBy(),
					'mapped'				=> false,
				)
			)
		;
	}

}
