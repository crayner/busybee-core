<?php

namespace Busybee\PersonBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Intl\Locale\Locale ;

class AddressType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder
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
			->add('addressList', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', 
				array(
					'data_class' => 'Busybee\PersonBundle\Entity\Address',
					'choices' => $options['data']->getRepository()->getAddressChoices(),
					'label' => 'address.label.choice',
					'placeholder' => 'address.placeholder.choice',
					'empty_data'  => null,
					'required' => false,
					'attr' => array(
						'help' => 'address.help.choice',
						'class' => 'beeAddressList'.$options['data']->getClassSuffix(),
					),
					'mapped' => false,
					'translation_domain' => 'BusybeePersonBundle',
				)
			)
			->add('save', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
					'label'					=> 'address.label.save', 
					'attr' 					=> array(
						'class' 				=> 'beeAddressSave'.$options['data']->getClassSuffix().' btn btn-primary glyphicons glyphicons-plus-sign',
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
				'allow_extra_fields' => true,
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
