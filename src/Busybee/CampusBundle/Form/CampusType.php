<?php

namespace Busybee\CampusBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Busybee\SystemBundle\Setting\SettingManager ;
use Doctrine\ORM\EntityRepository ;

class CampusType extends AbstractType
{
	/**
	 * @var	Busybee\SystemBundle\Setting\SettingManager 
	 */
	private $sm ;
	
	/**
	 * Construct
	 */
	public function __construct(SettingManager $sm)
	{
		$this->sm = $sm ;
	}

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('identifier', null, array(
					'label'		=> 'campus.label.identifier',
					'attr'		=> array(
						'help'		=> 'campus.help.identifier',
						'class'		=> 'locationForm',
					),
				)
			)
			->add('name', null, array(
					'label'		=> 'campus.label.name',
					'attr'		=> array(
						'help'		=> 'campus.help.name',
						'class'		=> 'locationForm',
					),
				)
			)
			->add('postcode', null, array(
					'label'		=> 'campus.label.postcode',
					'attr'		=> array(
						'help'		=> 'campus.help.postcode',
					),
				)
			)
			->add('territory', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
					'label' => 'campus.label.territory',
					'required' => false,
					'choices' => $this->sm->get('Address.TerritoryList'),
					'attr'		=> array(
						'help'		=> 'campus.help.territory',
						'class'		=> 'locationForm',
					),
				)
			)
			->add('locality', null, array(
					'label'		=> 'campus.label.locality',
					'attr'		=> array(
						'help'		=> 'campus.help.locality',
						'class'		=> 'locationForm',
					),
				)
			)
			->add('country', $this->sm->get('CountryType'), array(
					'label' => 'campus.label.country',
					'attr' => array(
						'help' => 'campus.help.country',
						'class'		=> 'locationForm',
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
					'label'					=> 'form.cancel', 
					'translation_domain' 	=> 'BusybeeHomeBundle',
					'attr' 					=> array(
						'formnovalidate' 		=> 'formnovalidate',
						'class' 				=> 'btn btn-info glyphicons glyphicons-remove-circle',
						'onClick'				=> "location.href='".$options['data']->cancelURL."'",
					),
				)
			)
			->add('locationList', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
					'class'			=> 'BusybeeCampusBundle:Campus',
					'label'			=> 'campus.label.locations', 
					'attr' 			=> array(
						'class' 			=> 'locationList',
					),
					'mapped'		=> false,
					'choice_label'	=> 'name',
					'query_builder' => function (EntityRepository $er) {
							return $er->createQueryBuilder('c')
								->orderBy('c.name', 'ASC');	
					},
					'placeholder'	=> 'Select a Location',
					'required'		=> false,
				)
			)
		;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Busybee\CampusBundle\Entity\Campus',
			'translation_domain' => 'BusybeeCampusBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'campus';
    }


}
