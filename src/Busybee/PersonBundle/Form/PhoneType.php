<?php

namespace Busybee\PersonBundle\Form;

use Busybee\PersonBundle\Events\PhoneSubscriber;
use Busybee\PersonBundle\Repository\PhoneRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
     * @var PhoneRepository
     */
    private $pr ;


    /**
	 * Construct
	 */
	public function __construct(SettingManager $sm, PhoneRepository $pr)
	{
		$this->sm = $sm ;
		$this->pr = $pr;
	}

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('phoneType', ChoiceType::class,
				array(
					'label' => 'person.label.phone.type',
					'choices' => $this->sm->get('Phone.TypeList'),
                )
			)
			->add('phoneNumber', null, 
				array(
					'label' => 'person.label.phone.number',
					'attr'	=> array(
						'help'	=> 'person.help.phone.number',
					),
				)
			)
			->add('countryCode', ChoiceType::class,
				array(
					'label' => 'person.label.phone.country',
					'required' => false,
					'choices' => $this->sm->get('Phone.CountryList'),
				)
			);
        $builder->get('phoneNumber')
            ->addModelTransformer(new PhoneTransformer());
        $builder->addEventSubscriber(new PhoneSubscriber($this->pr));
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
