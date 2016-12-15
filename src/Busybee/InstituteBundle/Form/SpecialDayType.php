<?php

namespace Busybee\InstituteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType ;
use Symfony\Component\Form\Extension\Core\Type\HiddenType ;
use Doctrine\ORM\EntityManager as ObjectManager;
use Busybee\InstituteBundle\Form\DataTransformer\YearTransformer ;

class SpecialDayType extends AbstractType
{
	/**
	 * @var	Doctrine\Common\Persistence\ObjectManager 
	 */
	private $manager ;
	
	/**
	 * Construct
	 */
	public function __construct( ObjectManager $manager)
	{
		$this->manager = $manager ;
	}

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$key = isset($options['property_path']) ? str_replace(array('[',']'), '', $options['property_path']) : '__name__' ;
		$year = $options['year_data'];
		if (is_null($year->getFirstDay()))
			$years = array(date('Y'));
		else
			$years = range($year->getFirstDay()->format('Y'), $year->getLastDay()->format('Y'));
       	$builder
			->add('day', null,
				array(
					'label'		=>	'specialDay.label.day',
					'attr'		=>	array(
						'help'		=> 'specialDay.help.day',
					),
					'years'		=>	$years,
				)
			)
			->add('type', ChoiceType::class,
				array(
					'label'		=>	'specialDay.label.type',
					'attr'		=>	array(
						'help'		=> 'specialDay.help.type',
						'class'		=> 'alterType' . $key,
					),
					'choices'	=>	array(
						'specialDay.type.closure'		=>	'closure',
						'specialDay.type.alter'			=>	'alter',
					),
				)
			)
			->add('name', null,
				array(
					'label'		=>	'specialDay.label.name',
					'attr'		=>	array(
						'help'		=> 'specialDay.help.name',
					),
				)
			)
			->add('description', null,
				array(
					'label'		=>	'specialDay.label.description',
					'attr'		=>	array(
						'help'		=> 'specialDay.help.description',
						'rows'		=> '3'
					),
					'required'	=> false,
				)
			)
			->add('open', null,
				array(
					'label'		=>	'specialDay.label.open',
					'attr'		=>	array(
						'help'		=> 'specialDay.help.open',
						'class'		=> 'alterTime',
					),
					'placeholder'	=> array('hour' => 'specialDay.hour', 'minute' => 'specialDay.minute'),
				)
			)
			->add('start', null,
				array(
					'label'		=>	'specialDay.label.start',
					'attr'		=>	array(
						'help'		=> 'specialDay.help.start',
						'class'		=> 'alterTime',
					),
					'placeholder'	=> array('hour' => 'specialDay.hour', 'minute' => 'specialDay.minute'),
				)
			)
			->add('finish', null,
				array(
					'label'		=>	'specialDay.label.finish',
					'attr'		=>	array(
						'help'		=> 'specialDay.help.finish',
						'class'		=> 'alterTime',
					),
					'placeholder'	=> array('hour' => 'specialDay.hour', 'minute' => 'specialDay.minute'),
				)
			)
			->add('close', null,
				array(
					'label'		=>	'specialDay.label.close',
					'attr'		=>	array(
						'help'		=> 'specialDay.help.close',
						'class'		=> 'alterTime',
					),
					'placeholder'	=> array('hour' => 'specialDay.hour', 'minute' => 'specialDay.minute'),
				)
			)
			->add('year', HiddenType::class)
        ;
        $builder->get('year')
            ->addModelTransformer(new YearTransformer($this->manager));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
			array(
				'data_class' 			=> 'Busybee\InstituteBundle\Entity\SpecialDay',
				'translation_domain'	=> 'BusybeeInstituteBundle',
				'year_data'				=> null,
        	)
		);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'specialday';
    }


}
