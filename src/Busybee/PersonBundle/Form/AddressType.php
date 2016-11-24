<?php

namespace Busybee\PersonBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Intl\Locale\Locale ;
use Busybee\SystemBundle\Setting\SettingManager;
use Busybee\FormBundle\Type\AutoCompleteType ;
use Busybee\PersonBundle\Entity\Address ;
use Busybee\PersonBundle\Entity\Locality ;
use Doctrine\Common\Persistence\ObjectManager;
use Busybee\PersonBundle\Form\DataTransformer\AddressTransformer ;

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
						'class' => 'beeBuildingType'.$options['classSuffix'],
					),
					'choices' => $this->sm->get('Address.BuildingType'),
				)
			)
			->add('buildingNumber', null, array(
					'label' => 'address.label.buildingNumber',
					'attr' => array(
						'help' => 'address.help.buildingNumber',
						'maxLength' => 10,
						'class' => 'beeBuildingNumber'.$options['classSuffix'],
					),
				)
			)
			->add('streetNumber', null, array(
					'label' => 'address.label.streetNumber',
					'attr' => array(
						'help' => 'address.help.streetNumber',
						'maxLength' => 10,
						'class' => 'beeStreetNumber'.$options['classSuffix'],
					),
				)
			)
			->add('propertyName', null, array(
					'label' => 'address.label.propertyName',
					'attr' => array(
						'help' => 'address.help.propertyName',
						'class' => 'beePropertyName'.$options['classSuffix'],
					),
					'required' => false,
				)
			)
			->add('streetName', null, array(
					'label' => 'address.label.streetName',
					'attr' => array(
						'help' => 'address.help.streetName',
						'class' => 'beeStreetName'.$options['classSuffix'],
					),
				)
			)
			->add('addressList', AutoCompleteType::class, 
				array(
					'class' => 'Busybee\PersonBundle\Entity\Address',
					'label' => 'address.label.choice',
					'choice_label' => 'singleLineAddress',
					'empty_data'  => null,
					'required' => false,
					'attr' => array(
						'help' => 'address.help.choice',
						'class' => 'beeAddressList'.$options['classSuffix'],
					),
					'mapped' => false,
					'hidden' => array(
						'name' => 'person['.$options['classSuffix'].'][AddressValue]',
						'value' => ($options['data'] instanceof Address && $options['data']->getId() > 0 ? $options['data']->getId() : 0),
						'class' => 'beeAddressValue'.$options['classSuffix'],
					),
				)
			)
			->add('save', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
					'label'					=> 'address.label.save', 
					'attr' 					=> array(
						'class' 				=> 'beeAddressSave'.$options['classSuffix'].' btn btn-primary glyphicons glyphicons-plus-sign',
						'style'					=>	'width: 146px;',
					),
				)
			)
			->add('clear', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
					'label'					=> 'address.label.clear', 
					'attr' 					=> array(
						'class' 				=> 'beeAddressClear'.$options['classSuffix'].' btn btn-warning glyphicons glyphicons-restart',
						'style'					=>	'width: 146px;',
					),
				)
			)
		;
		if ($options['data'] instanceof Address) {
			$builder->add('locality', 'Busybee\PersonBundle\Form\LocalityType',
				array(
						'data' => $options['data']->getLocality(),
						'classSuffix' => $options['classSuffix'],
				)
			);
		} else {
			$builder->add('locality', 'Busybee\PersonBundle\Form\LocalityType', 
				array(
						'data' => new Locality(),
						'classSuffix' => $options['classSuffix'],
				)
			);
		}
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
				'data_class' 			=> 'Busybee\PersonBundle\Entity\Address',
				'translation_domain' 	=> 'BusybeePersonBundle',
				'classSuffix'			=> null,
				'validation_groups' 	=> array('person_form'),
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
