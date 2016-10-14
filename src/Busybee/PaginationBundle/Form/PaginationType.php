<?php

namespace Busybee\PaginationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Busybee\DatabaseBundle\Entity\TableRepository;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;


class PaginationType extends AbstractType
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
		$builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
				$event->stopPropagation();
			}, 900); // Always set a higher priority than ValidationListener
		return $builder;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
				'translation_domain' 	=> 'BusybeePaginationBundle',
				'validation_groups'		=> null,
				'attr'					=> array(
					'class'					=> 'ajaxForm form-inline',
					'novalidator'			=> 'novalidator',
				),
			)
		);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'paginator';
    }


	/**
	 * @return FormBuilderInterface
	 */
	protected function addSort(FormBuilderInterface $builder, $options) 
	{
		return $builder	
            ->add('current_sort', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
					'label'					=> 'pagination.sort',
					'choices'				=> $options['data']->getSortList(),
					'required'				=> true,
					'render_optional_text' 	=> false,
					'attr'					=> array(
//						'disabled'				=> 'disabled',
						'class'					=> 'input-sm',
						'onChange'				=> '$(this.form).submit()',
					),
					'data'					=> $options['data']->getSortBy(),
					'mapped'				=> false,
				)
			)
		;
	}

	/**
	 * @return FormBuilderInterface
	 */
	protected function addSearch(FormBuilderInterface $builder, $options) 
	{
		return $builder	
            ->add('current_search', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
					'label'					=> 'pagination.search',
					'required'				=> false,
					'mapped'				=> false,
					'render_optional_text'	=> false,
					'attr'					=> array(
						'placeholder'			=> 'pagination.placeholder.search',
						'class'					=> 'input-sm form-inline',
					),
				)
			)
        ;
	}

	/**
	 * @return FormBuilderInterface
	 */
	protected function addHidden(FormBuilderInterface $builder, $options) 
	{
		return $builder	
			->add('next', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label'				=> 'form.next',
					'attr' 					=> array(
						'class' 				=> 'btn btn-info glyphicon glyphicon-forward input-sm'
					),
					'translation_domain' 	=> 'BusybeeDisplayBundle',
				)
			)
			->add('prev', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label'				=> 'form.prev',
					'attr' 					=> array(
						'class' 				=> 'btn btn-info glyphicon glyphicon-backward input-sm'
					),
					'translation_domain' 	=> 'BusybeeDisplayBundle',
				)
			)
			->add('offset', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
					'data'					=> $options['data']->getOffset(),
				)
			)
			->add('total', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
					'data'					=> $options['data']->getTotal(),
				)
			)
			->add('last_search', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
					'data'					=> $options['data']->getSearch(),
				)
			)
			->add('start_search', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label'				=> 'form.start_search',
					'attr' 					=> array(
						'class' 				=> 'btn btn-success glyphicon glyphicon-search input-sm  form-inline'
					),
					'translation_domain' 	=> 'BusybeeDisplayBundle',
				)
			)
		;
	}
    
	/**
	 * @return FormBuilderInterface
	 */
	protected function addLimit(FormBuilderInterface $builder, $options) 
	{
		$choices = array();
		$choices[10] = '10';
		if ($options['data']->getTotal() > 10)
			$choices[25] = '25';
		if ($options['data']->getTotal() > 25)
			$choices[100] = '100';
		if( $options['data']->getTotal() > 10)
			if (!empty($choices[$options['data']->getLimit()]) )
				return $builder	
					->add('limit', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
							'label'					=> 'pagination.limit',
							'choices'				=> $choices,
							'required'				=> true,
							'attr'					=> array(
//								'disabled'				=> 'disabled',
								'style'					=> 'width: 75px;',
								'class'					=> 'input-sm',
								'onChange'				=> '$(this.form).submit()',
							),
							'data'					=> $options['data']->getLimit(),
						)
					)
				;
			else 
				return $builder	
					->add('limit', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
							'label'					=> 'pagination.limit',
							'choices'				=> $choices,
							'required'				=> true,
							'attr'					=> array(
//								'disabled'				=> 'disabled',
								'style'					=> 'width: 100px;',
								'class'					=> 'input-sm',
								'onChange'				=> '$(this.form).submit()',
							),
						)
					)
				;
		else
			return $builder	
				->add('limit', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
						'data'					=> 10,
					)
				)
			;
	}

}
