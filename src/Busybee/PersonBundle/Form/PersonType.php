<?php

namespace Busybee\PersonBundle\Form ;

use Busybee\FormBundle\Type\AutoCompleteType;
use Busybee\FormBundle\Type\YesNoType;
use Busybee\PersonBundle\Entity\Address;
use Busybee\PersonBundle\Entity\Staff;
use Busybee\PersonBundle\Events\PersonSubscriber;
use Busybee\PersonBundle\Form\DataTransformer\PersonTypeBooleanTransformer;
use Busybee\PersonBundle\Model\PhotoUploader;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Symfony\Component\Form\AbstractType ;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface ;
use Symfony\Component\OptionsResolver\OptionsResolver ;
use Symfony\Component\Validator\Constraints as Assert;
use Busybee\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Persistence\ObjectManager ;


class PersonType extends AbstractType
{
	/**
	 * @var	SettingManager
	 */
	private $sm ;

    /**
     * @var	ObjectManager
     */
    private $manager ;

    /**
     * @var	PhotoUpLoader
     */
    private $photoLoader ;

	/**
	 * Construct
	 */
	public function __construct(SettingManager $sm, ObjectManager $manager, PhotoUploader $photoLoader)
	{
		$this->sm = $sm ;
		$this->manager = $manager ;
		$this->photoLoader = $photoLoader;
	}

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', ChoiceType::class, array(
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
			->add('gender', ChoiceType::class, array(
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
			->add('address1', AutoCompleteType::class,
                array(
                    'class' => Address::class,
                    'data'  =>  $options['data']->getAddress1(),
                    'choice_label' => 'singleLineAddress',
                    'empty_data'  => null,
                    'required' => false,
                    'attr' => array(
                        'help' => 'person.help.address1',
                        'class' => 'beeAddressList formChanged',
                    ),
                    'data' => $options['data']->getAddress1(),
                )
            )
            ->add('address2', AutoCompleteType::class,
                array(
                    'class' => Address::class,
                    'choice_label' => 'singleLineAddress',
                    'data'  => $options['data']->getAddress2(),
                    'empty_data'  => null,
                    'required' => false,
                    'attr' => array(
                        'help' => 'person.help.address2',
                        'class' => 'beeAddressList formChanged',
                    ),
                    'data' => $options['data']->getAddress2(),
                )
            )
            ->add('phone', CollectionType::class, array(
                    'label'					=> 'person.label.phones',
                    'entry_type'			=> PhoneType::class,
                    'allow_add'				=> true,
                    'by_reference'			=> false,
                    'allow_delete'			=> true,
                    'attr'                  => array(
                        'class'                 => 'phoneNumberList'
                    ),
                )
            )
            ->add('staff', HiddenType::class)
            ->add('staffQuestion', YesNoType::class, array(
                    'label'					=> 'person.label.staff',
                    'attr'                  => array(
                        'help'                  => 'person.help.staff',
                        'data-off-icon-cls'	 	=> "halflings-thumbs-down",
                        'data-on-icon-cls' 		=> "halflings-thumbs-up",
                    ),
                    'mapped'                => false,
                )
            )
		;
        $builder->get('staff')->addModelTransformer(new EntityToStringTransformer($this->manager, Staff::class));
        $builder->addEventSubscriber(new PersonSubscriber($this->photoLoader, $this->manager));
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
				'allow_extra_fields' 	=> true,
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
