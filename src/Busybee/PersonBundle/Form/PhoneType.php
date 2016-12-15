<?php

namespace Busybee\PersonBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Busybee\SystemBundle\Setting\SettingManager;
use Busybee\PersonBundle\Form\DataTransformer\PhoneTransformer ;

class PhoneType extends AbstractType
{
	/**
	 * @var SettingManager
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
			->add('phoneType', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', 
				array(
					'label' => 'person.label.phone.type',
					'choices' => $this->sm->get('Phone.TypeList'),
				)
			)
			->add('phoneNumber', null, 
				array(
					'label' => 'person.label.phone.number',
					'attr'	=> array(
						'readonly' => 'readonly',
						'help'	=> 'person.help.phone.number',					
					),
				)
			)
			->add('countryCode', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', 
				array(
					'label' => 'person.label.phone.country',
					'required' => false,
					'choices' => $this->sm->get('Phone.CountryList'),
				)
			);
        $builder->get('phoneNumber')
            ->addModelTransformer(new PhoneTransformer());
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
			array(
				'data_class' => 'Busybee\PersonBundle\Entity\Phone',
				'translation_domain' => 'BusybeePersonBundle',
			)
		);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'phone';
    }


}
