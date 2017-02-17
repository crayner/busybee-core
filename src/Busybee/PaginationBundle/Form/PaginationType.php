<?php

namespace Busybee\PaginationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
	 * @return FormBuilderInterface
	 */
    protected function addLimit(FormBuilderInterface $builder, $options)
	{
		$choices = array();
		$choices[10] = '10';
		$choices[25] = '25';
		$choices[100] = '100';
		$limit = $options['data']->getLimit() < 10 ? 10 : $options['data']->getLimit() ;
		$choices[$limit] = $limit;
		ksort($choices,	SORT_NUMERIC);
		if( $options['data']->getTotal() > 10)
            return $builder
                ->add('limit', ChoiceType::class, array(
							'label'					=> 'pagination.limit',
							'choices'				=> $choices,
							'required'				=> true,
							'attr'					=> array(
//								'disabled'				=> 'disabled',
								'onChange'				=> '$(this.form).submit()',
							),
							'data'					=> $limit,
						)
					)
					->add('lastLimit', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
							'data'					=> $limit,
						)
					)
				;
		else
            return $builder
				->add('limit', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
						'data'					=> $limit,
					)
				)
				->add('lastLimit', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
						'data'					=> $limit,
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
            ->add('offSet', HiddenType::class, array(
                    'data' => $options['data']->getOffSet(),
                )
            )
            ->add('total', HiddenType::class, array(
                    'data' => $options['data']->getTotal(),
                )
            )
            ->add('lastSearch', HiddenType::class, array(
                    'data' => $options['data']->getSearch(),
                )
            );
    }

    /**
     * @return FormBuilderInterface
     */
    protected function addSort(FormBuilderInterface $builder, $options)
    {
        return $builder
            ->add('currentSort', ChoiceType::class, array(
                    'label' => 'pagination.sort',
                    'choices' => $options['data']->getSortList(),
                    'required' => true,
                    'attr' => array(
//						'disabled'				=> 'disabled',
                        'class' => 'form-inline',
                        'onChange' => '$(this.form).submit()',
                    ),
                    'data' => $options['data']->getSortByName(),
                    'mapped' => false,
                )
            );
    }

    /**
     * @return FormBuilderInterface
     */
    protected function addSearch(FormBuilderInterface $builder, $options)
    {
        return $builder
            ->add('currentSearch', null, array(
                    'label' => 'pagination.search',
                    'required' => false,
                    'mapped' => false,
                    'attr' => array(
                        'placeholder' => 'pagination.placeholder.search',
                        'class' => 'form-inline',
                    ),
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'translation_domain' => 'BusybeePaginationBundle',
                'validation_groups' => null,
                'attr' => array(
                    'class' => 'ajaxForm form-inline',
                    'novalidator' => 'novalidator',
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

}
