<?php

namespace Busybee\PersonBundle\Form ;

use Busybee\FormBundle\Type\AutoCompleteType;
use Busybee\PersonBundle\Entity\Address;
use Busybee\PersonBundle\Events\PersonSubscriber;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType ;
use Symfony\Component\Form\FormBuilderInterface ;
use Symfony\Component\OptionsResolver\OptionsResolver ;
use Busybee\PersonBundle\Form\Transformer\PhotoTransformer ;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine ;
use Symfony\Component\Validator\Constraints as Assert;
use Busybee\SystemBundle\Setting\SettingManager;
use Busybee\PersonBundle\Form\AddressType ;
use Doctrine\Common\Persistence\ObjectManager ;
use Busybee\PersonBundle\Form\DataTransformer\AddressTransformer ;
use Symfony\Component\Form\Extension\Core\Type\HiddenType ;

class PersonType extends AbstractType
{
	/**
	 * @var	Busybee\SystemBundle\Setting\SettingManager 
	 */
	private $sm ;
	
	/**
	 * @var	Doctrine\Common\Persistence\ObjectManager 
	 */
	private $manager ;
	
	/**
	 * Construct
	 */
	public function __construct(SettingManager $sm, ObjectManager $manager)
	{
		$this->sm = $sm ;
		$this->manager = $manager ;
	}

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
					'label' => 'person.label.title',
					'choices' => $this->sm->get('Person.TitleList'),
					'attr'	=> array(
						'class' => 'beeTitle',
					),
					'required' => false,
				)
			)
			->add('surname', null, array(
					'label' => 'person.label.surname',
					'attr'	=> array(
						'class' => 'beeSurname',
					),
				)
			)
			->add('firstName', null, array(
					'label' => 'person.label.firstName',
					'attr'	=> array(
						'class' => 'beeFirstName',
					),
				)
			)
			->add('preferredName', null, array(
					'label' => 'person.label.preferredName',
					'attr'	=> array(
						'class' => 'beePreferredName',
					),
					'required' => false,
				)
			)
			->add('officialName', null, array(
					'label' => 'person.label.officialName',
					'attr' => array(
						'help' => 'person.help.officialName',
						'class' => 'beeOfficialName',
					),
				)
			)
			->add('gender', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
					'choices' => $this->sm->get('Person.GenderList'),
					'label' => 'person.label.gender',
					'attr'	=> array(
						'class' => 'beeGender',
					),
				)
			)
			->add('dob', 'Symfony\Component\Form\Extension\Core\Type\BirthdayType', array(
					'label' => 'person.dob.label',
					'required' => false,
					'attr'	=> array(
						'class' => 'beeDob',
					),
				)
			)
			->add('email', 'Symfony\Component\Form\Extension\Core\Type\EmailType', array(
					'label' => 'person.label.email',
					'required' => false,
				)
			)
			->add('email2', 'Symfony\Component\Form\Extension\Core\Type\EmailType', array(
					'label' => 'person.label.email2',
					'required' => false,
				)
			)
			->add('photo', 'Busybee\FormBundle\Type\ImageType', array(
					'attr' => array(
						'help' => 'person.help.photo' ,
						'imageClass' => 'headShot75',
					),
					'label' => 'person.label.photo',
					'required' => false,
				)
			)
			->add('website', null, array(
					'label' => 'person.label.website',
					'required' => false,
				)
			)
            ->add('address1', HiddenType::class,
                array(
                    'error_mapping' => array(
                        '.' => 'address1_list'
                    )
                )
            )
			->add('address1_list', AutoCompleteType::class,
                array(
                    'class' => 'Busybee\PersonBundle\Entity\Address',
                    'label' => 'address.label.address1',
                    'choice_label' => 'singleLineAddress',
                    'empty_data'  => null,
                    'required' => false,
                    'attr' => array(
                        'help' => 'address.help.address1',
                        'class' => 'beeAddressList formChanged',
                    ),
                    'mapped' => false,
                    'hidden' => array(
                        'name' => "address1_stuff",
                        'value' => 0,
                        'class' => '',
                    ),
                )
            )
            ->add('address2', HiddenType::class,
                array(
                    'error_mapping' => array(
                        '.' => 'address2_list'
                    )
                )
            )
            ->add('address2_list', AutoCompleteType::class,
                array(
                    'class' => 'Busybee\PersonBundle\Entity\Address',
                    'label' => 'address.label.address2',
                    'choice_label' => 'singleLineAddress',
                    'empty_data'  => null,
                    'required' => false,
                    'attr' => array(
                        'help' => 'address.help.address2',
                        'class' => 'beeAddressList formChanged',
                    ),
                    'mapped' => false,
                    'hidden' => array(
                        'name' => "address2_stuff",
                        'value' => 0,
                        'class' => '',
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
			->add('phone', 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
					'label'					=> 'person.label.phones', 
					'entry_type'			=> 'Busybee\PersonBundle\Form\PhoneType',
					'allow_add'				=> true,
					'by_reference'			=> false,
					'allow_delete'			=> true,
				)
			)
		;
        $transformer = new EntityToStringTransformer($this->manager);
        $transformer->setEntityClass(Address::class);
        $builder->get('address1')->addModelTransformer($transformer);

        $transformer2 = new EntityToStringTransformer($this->manager);
        $transformer2->setEntityClass(Address::class);
        $builder->get('address2')->addModelTransformer($transformer2);

        $builder->addEventSubscriber(new PersonSubscriber());
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
			array(
				'data_class' 			=> 'Busybee\PersonBundle\Entity\Person',
				'translation_domain'	=> 'BusybeePersonBundle',
				'allow_extra_fields' 	=> false,
			)
		);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'person';
    }
    /**
     * {@inheritdoc}
     */
    public function getYears()
    {
        $years = array();
		for($i=-100; $i<=0; $i++)
		{
			$years[] = date('Y', strtotime($i.' Years'));
		}
		return $years ;
    }
}
