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
					),
				)
			)
			->add('line2', null, array(
					'label' => 'address.label.line2',
					'attr' => array(
						'help' => 'address.help.line2',
					),
				)
			)
			->add('locality', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
					'label' => 'address.label.locality',
					'class' => 'Busybee\PersonBundle\Entity\Locality',
//					'choices' => $options['data']->getLocality()->getLocalities(),
//					'data' => $options['data']->getLocality(),
				)
			)
		;
		dump($options);
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
        return 'busybee_address';
    }


}
