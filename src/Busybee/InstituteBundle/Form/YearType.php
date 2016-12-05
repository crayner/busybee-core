<?php

namespace Busybee\InstituteBundle\Form ;

use Symfony\Component\Form\AbstractType ;
use Symfony\Component\Form\FormBuilderInterface ;
use Symfony\Component\OptionsResolver\OptionsResolver ;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType ;
use Symfony\Component\Form\Extension\Core\Type\CollectionType ;
use Busybee\InstituteBundle\Validator\CalendarStatus ;
use Busybee\InstituteBundle\Validator\CalendarDate ;
use Busybee\InstituteBundle\Validator\TermDate ;
use Busybee\InstituteBundle\Validator\SpecialDayDate ;
use Busybee\InstituteBundle\Form\TermType ;
use Busybee\InstituteBundle\Form\SpecialDayType ;

class YearType extends AbstractType
{
	private	$statusList ;
	
	public function __construct($list)
	{
		$this->statusList = $list ;
	}
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('name', null, 
				array(
					'label'	=>	'calendar.label.name',
					'attr'	=>	array(
						'help'	=>	'calendar.help.name',
					),
				)
			)
			->add('firstDay', null, 
				array(
					'label'	=>	'calendar.label.firstDay',
					'attr'	=>	array(
						'help'	=>	'calendar.help.firstDay',
					),
				)
			)
			->add('lastDay', null, 
				array(
					'label'	=>	'calendar.label.lastDay',
					'attr'	=>	array(
						'help'	=>	'calendar.help.lastDay',
					),
					'constraints'	=> array(
						new CalendarDate(array('fields' => $options['data'])),
					),
				)
			)
			->add('status', ChoiceType::class,
				array(
					'label'	=>	'calendar.label.status',
					'attr'	=>	array(
						'help'	=>	'calendar.help.status',
					),
					'choices'	=> $this->statusList,
					'placeholder'	=> 'calendar.placeholder.status',
					'constraints'	=> array(
						new CalendarStatus(array('id' => is_null($options['data']->getId()) ? 'Add' : $options['data']->getId())),
					),
				)
			)
			->add('save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label' 				=> 'form.save',
					'translation_domain' 	=> 'BusybeeHomeBundle',
					'attr' 					=> array(
						'class' 				=> 'btn btn-success glyphicons glyphicons-disk-save',
					),
				)
			)
			->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
					'label'					=> 'form.reset', 
					'translation_domain' 	=> 'BusybeeHomeBundle',
					'attr' 					=> array(
						'formnovalidate' 		=> 'formnovalidate',
						'class' 				=> 'btn btn-info glyphicons glyphicons-remove-circle',
						'onClick'				=> "location.href='".$options['data']->cancelURL."'",
					),
				)
			)
			->add('terms', CollectionType::class, array(
					'entry_type'	=> TermType::class,
					'allow_add'		=> true,
					'entry_options'	=> array(
						'year_data'	=>	$options['data'],
					),
					'constraints'	=> array(
						new TermDate($options['data']),
					),
					'label'			=> false, 
				)
			)
			->add('specialDays', CollectionType::class, array(
					'entry_type'	=> SpecialDayType::class,
					'allow_add'		=> true,
					'entry_options'	=> array(
						'year_data'	=>	$options['data'],
					),
					'constraints'	=> array(
						new SpecialDayDate($options['data']),
					),
					'label'			=> false, 
					'allow_delete'	=> true,
					'mapped'		=> true,
				)
			)
			;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
			array(
				'data_class' 			=> 'Busybee\InstituteBundle\Entity\Year',
				'translation_domain' 	=> 'BusybeeInstituteBundle',
			)
		);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'calendar_year';
    }


}
