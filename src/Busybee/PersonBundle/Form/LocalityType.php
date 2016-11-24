<?php

namespace Busybee\PersonBundle\Form ;

use Symfony\Component\Form\AbstractType ;
use Symfony\Component\Form\FormBuilderInterface ;
use Symfony\Component\OptionsResolver\OptionsResolver ;
use Symfony\Component\Intl\Locale\Locale ;
use Symfony\Component\Form\FormEvent ;
use Symfony\Component\Form\FormEvents ;
use Busybee\SystemBundle\Setting\SettingManager ;
use Busybee\PersonBundle\Repository\LocalityRepository ;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ;


class LocalityType extends AbstractType
{
	/**
	 * @var	Busybee\SystemBundle\Setting\SettingManager 
	 */
	private $sm ;
	/**
	 * @var	Busybee\PersonBundle\Repository\LocalityRepository 
	 */
	private $lr ;
	
	/**
	 * Construct
	 */
	public function __construct(SettingManager $sm, LocalityRepository $lr)
	{
		$this->sm = $sm ;
		$this->lr = $lr ;
	}

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('locality', null, array(
					'label' => 'locality.label.locality',
					'required' => false,
					'attr' => array(
						'class' => 'beeLocality'.$options['classSuffix'],
					),
				)
			)
			->add('territory', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
					'label' => 'locality.label.territory',
					'required' => false,
					'attr' => array(
						'class' => 'beeTerritory'.$options['classSuffix'],
					),
					'choices' => $this->sm->get('Address.TerritoryList'),
				)
			)
			->add('postCode', null, array(
					'label' => 'locality.label.postcode',
					'required' => false,
					'attr' => array(
						'class' => 'beePostCode'.$options['classSuffix'],
					),
				)
			)
			->add('country', 'Symfony\Component\Form\Extension\Core\Type\CountryType', array(
					'label' => 'locality.label.country',
					'required' => false,
					'attr' => array(
						'class' => 'beeCountry'.$options['classSuffix'],
					),
				)
			)
			->add('localityList', EntityType::class, 
				array(
					'class' => 'BusybeePersonBundle:Locality',
					'label' => 'locality.label.choice',
					'choice_label'	=> 'fullLocality',
					'placeholder' => 'locality.placeholder.choice',
					'required' => false,
					'attr' => array(
						'help' => 'locality.help.choice',
						'class' => 'beeLocalityList'.$options['classSuffix'],
					),
					'mapped' => false,
					'translation_domain' => 'BusybeePersonBundle',
				)
			)
			->add('save', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
					'label'					=> 'locality.label.save', 
					'attr' 					=> array(
						'class' 				=> 'beeLocalitySave'.$options['classSuffix'].' btn btn-info glyphicons glyphicons-plus-sign',
					),
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
			array (
				'data_class' => 'Busybee\PersonBundle\Entity\Locality',
				'translation_domain' => 'BusybeePersonBundle',
				'allow_extra_fields' => true,
				'classSuffix'	=> null,
			)
		);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'locality';
    }
}
