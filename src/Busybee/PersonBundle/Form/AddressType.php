<?php

namespace Busybee\PersonBundle\Form;

use Busybee\FormBundle\Type\AutoCompleteType;
use Busybee\FormBundle\Type\SettingChoiceType;
use Busybee\PersonBundle\Entity\Address;
use Busybee\PersonBundle\Entity\Locality;
use Busybee\PersonBundle\Events\AddressSubscriber;
use Busybee\PersonBundle\Repository\LocalityRepository;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\SecurityBundle\Form\ResetType;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Busybee\SystemBundle\Setting\SettingManager;

class AddressType extends AbstractType
{
    /**
     * @var	SettingManager
     */
    private $sm ;
    /**
     * @var	EntityManager
     */
    private $em ;

	/**
	 * Construct
	 */
	public function __construct(SettingManager $sm, EntityManager $em)
	{
		$this->sm = $sm ;
		$this->lr = $em->getRepository('BusybeePersonBundle:Locality') ;
		$this->em = $em;
	}

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('buildingType', SettingChoiceType::class,
                array(
					'label' => 'address.label.buildingType',
					'attr' => array(
						'help' => 'address.help.buildingType',
						'class' => 'beeBuildingType monitorChange',
					),
                    'settingName' => 'Address.BuildingType',
                    'required'  =>  false,
				)
			)
			->add('buildingNumber', null, array(
					'label' => 'address.label.buildingNumber',
					'attr' => array(
						'help' => 'address.help.buildingNumber',
						'maxLength' => 10,
						'class' => 'beeBuildingNumber monitorChange',
					),
                    'required' => false,
				)
			)
			->add('streetNumber', null, array(
					'label' => 'address.label.streetNumber',
					'attr' => array(
						'help' => 'address.help.streetNumber',
						'maxLength' => 10,
						'class' => 'beeStreetNumber monitorChange',
					),
                    'required' => false,
				)
			)
			->add('propertyName', null, array(
					'label' => 'address.label.propertyName',
					'attr' => array(
						'help' => 'address.help.propertyName',
						'class' => 'beePropertyName monitorChange',
					),
					'required' => false,
				)
			)
			->add('streetName', null, array(
					'label' => 'address.label.streetName',
					'attr' => array(
						'help' => 'address.help.streetName',
						'class' => 'beeStreetName monitorChange',
					),
				)
			)
            ->add('locality', EntityType::class,
                array(
                    'class' => Locality::class,
                    'label' => 'address.label.locality',
                    'choice_label'	=> 'fullLocality',
                    'placeholder' => 'address.placeholder.locality',
                    'attr' => array(
                        'help' => 'address.help.locality',
                        'class' => 'beeLocality monitorChange',
                    ),
                    'query_builder' => function (LocalityRepository $lr) {
                        return $lr->createQueryBuilder('l')
                            ->orderBy('l.name', 'ASC')
                            ->addOrderBy('l.postCode', 'ASC')
                        ;
                    },
                )
            )
            ->add('save', SubmitType::class, array(
                    'label'					=> 'form.save',
                    'attr' 					=> array(
                        'class' 				=> 'beeAddressSave btn btn-success glyphicons glyphicons-plus-sign',
                    ),
                    'translation_domain' => 'BusybeeHomeBundle',
                )
            )
            ->add('close', ButtonType::class, array(
                    'label'					=> 'form.close',
                    'attr' 					=> array(
                        'class' 				=> 'formChanged beeAddressSave btn btn-warning glyphicons glyphicons-folder-closed',
                        'onclick'               => "window.close()",
                    ),
                    'translation_domain'    => 'BusybeeHomeBundle',
                )
            )
            ->add('reset', ResetType::class, array(
                    'label'					=> 'form.reset',
                    'attr' 					=> array(
                        'class' 				=> 'beeAddressSave btn btn-info glyphicons glyphicons-refresh',
                    ),
                    'translation_domain' => 'BusybeeHomeBundle',
                    'mapped'            => false,
                )
            )
            ->add('addressList', AutoCompleteType::class,
                array(
                    'class' => 'Busybee\PersonBundle\Entity\Address',
                    'label' => 'address.label.edit',
                    'choice_label' => 'singleLineAddress',
                    'empty_data'  => null,
                    'required' => false,
                    'attr' => array(
                        'help' => 'address.help.edit',
                        'class' => 'beeAddressList formChanged',
                    ),
                    'mapped' => false,
                )
            )
		;

		$builder->get('locality')->addModelTransformer(new EntityToStringTransformer($this->em, Locality::class));
        $builder->addEventSubscriber(new AddressSubscriber() );

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
				'allow_extra_fields' 	=> true,
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
