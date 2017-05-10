<?php

namespace Busybee\PersonBundle\Form;

use Busybee\FormBundle\Type\ToggleType;
use Busybee\InstituteBundle\Entity\Year;
use Busybee\InstituteBundle\Form\YearEntityType;
use Busybee\PersonBundle\Entity\Person;
use Busybee\PersonBundle\Events\UserSubscriber;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\SecurityBundle\Form\DirectRoleType;
use Busybee\SecurityBundle\Form\GroupType;
use Busybee\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $manager ;

    /**
     * @var SettingManager
     */
    private $sm ;

    /**
     * UserType constructor.
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager, SettingManager $sm)
    {
        $this->manager = $manager ;
        $this->sm = $sm ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array(
                    'label' 				=> 'user.label.username',
                    'attr'					=> array(
                        'help' 					=> 'user.help.username',
                        'class'                 => 'user',
                    ),
                    'required' 				=> true,
                )
            )
            ->add('usernameCanonical',HiddenType::class,
                array(
                   'attr'					=> array(
                        'class'                 => 'user',
                    ),
                )
            )
            ->add('email', HiddenType::class, array(
                    'attr'					=> array(
                        'class'                 => 'user',
                    ),
                )
            )
            ->add('emailCanonical', HiddenType::class, array(
                    'attr'					=> array(
                        'class'                 => 'user',
                    ),
                )
            )
            ->add('directroles', DirectRoleType::class)
            ->add('groups', GroupType::class)
            ->add('enabled', ToggleType::class,
                array(
                    'label' 				=> 'user.label.enabled',
                    'attr'					=> array(
                        'help' => 'user.help.enabled',
                        'class' => 'user',
                        'data-size' => 'mini',
                    ),
                )
            )
            ->add('locale', LocaleType::class,
                array(
                    'label' 				=> 'user.label.locale',
                    'attr'					=> array(
                        'help' 					=> 'user.help.locale',
                        'class'                 => 'user',
                    ),
                )
            )
            ->add('password', HiddenType::class,
                array(
                    'attr'  =>  array(
                        'class' => 'user',
                    )
                )
            )
            ->add('locked', ToggleType::class,
                array(
                    'label' 				=> 'user.label.locked',
                    'attr'					=> array(
                        'help' 					=> 'user.help.locked',
                        'class'                 => 'user',
                        'data-size' => 'mini',
                    ),
                )
            )
            ->add('expired', ToggleType::class,
                array(
                    'label' 				=> 'user.label.expired',
                    'attr'					=> array(
                        'help' 					=> 'user.help.expired',
                        'class'                 => 'user',
                        'data-size' => 'mini',
                    ),
              )
            )
            ->add('credentials_expired', ToggleType::class,
                array(
                    'label' 				=> 'user.label.credentials_expired',
                    'attr'					=> array(
                        'help' 					=> 'user.help.credentials_expired',
                        'class'                 => 'user',
                        'data-size' => 'mini',
                    ),
                )
            )
            ->add('person', HiddenType::class,
                array(
                    'attr'  =>  array(
                        'class' => 'user',
                    )
                )
            )
            ->add('year', YearEntityType::class, [
                    'placeholder' => 'user.placeholder.year',
                    'label' => 'user.label.year',
                    'attr' =>
                        [
                            'help' => 'user.help.year',
                        ],
                    'required' => false,
                    'translation_domain' => 'BusybeeSecurityBundle',
                ]
            )
        ;
        $builder->get('person')->addModelTransformer(new EntityToStringTransformer($this->manager, Person::class));

        $builder->addEventSubscriber(new UserSubscriber());
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
				'data_class' 			=> 'Busybee\SecurityBundle\Entity\User',
				'translation_domain' 	=> 'BusybeeSecurityBundle',
			)
		);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user';
    }
}
