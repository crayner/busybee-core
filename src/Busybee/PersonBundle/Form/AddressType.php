<?php

namespace Busybee\PersonBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Intl\Locale\Locale ;
use Busybee\SystemBundle\Setting\SettingManager;

class AddressType extends AbstractType
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
			->add('buildingType', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
					'label' => 'address.label.buildingType',
					'attr' => array(
						'help' => 'address.help.buildingType',
						'class' => 'beeBuildingType'.$options['data']->getClassSuffix(),
					),
					'choices' => $this->sm->get('Address.BuildingType'),
				)
			)
			->add('buildingNumber', null, array(
					'label' => 'address.label.buildingNumber',
					'attr' => array(
						'help' => 'address.help.buildingNumber',
						'maxLength' => 10,
						'class' => 'beeBuildingNumber'.$options['data']->getClassSuffix(),
					),
				)
			)
			->add('streetNumber', null, array(
					'label' => 'address.label.streetNumber',
					'attr' => array(
						'help' => 'address.help.streetNumber',
						'maxLength' => 10,
						'class' => 'beeStreetNumber'.$options['data']->getClassSuffix(),
					),
				)
			)
			->add('line1', null, array(
					'label' => 'address.label.line1',
					'attr' => array(
						'help' => 'address.help.line1',
						'class' => 'beeLine1'.$options['data']->getClassSuffix(),
					),
				)
			)
			->add('line2', null, array(
					'label' => 'address.label.line2',
					'attr' => array(
						'help' => 'address.help.line2',
						'class' => 'beeLine2'.$options['data']->getClassSuffix(),
					),
					'required' => false,
				)
			)
			->add('locality', 'Busybee\PersonBundle\Form\LocalityType', array(
					'data' => $options['data']->localityRecord,
				)
			)
			->add('addressList', 'Symfony\Component\Form\Extension\Core\Type\TextType', 
				array(
					'data_class' => 'Busybee\PersonBundle\Entity\Address',
					'label' => 'address.label.choice',
					'empty_data'  => null,
					'required' => false,
					'attr' => array(
						'help' => 'address.help.choice',
						'class' => 'beeAddressList'.$options['data']->getClassSuffix(),
					),
					'mapped' => false,
				)
			)
			->add('save', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
					'label'					=> 'address.label.save', 
					'attr' 					=> array(
						'class' 				=> 'beeAddressSave'.$options['data']->getClassSuffix().' btn btn-primary glyphicons glyphicons-plus-sign',
						'style'					=>	'width: 146px;',
					),
				)
			)
			->add('clear', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
					'label'					=> 'address.label.clear', 
					'attr' 					=> array(
						'class' 				=> 'beeAddressClear'.$options['data']->getClassSuffix().' btn btn-warning glyphicons glyphicons-restart',
						'style'					=>	'width: 146px;',
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
        $resolver->setDefaults(array(
				'data_class' => 'Busybee\PersonBundle\Entity\Address',
				'translation_domain' 	=> 'BusybeePersonBundle',
			)
		);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'address';
    }


}
