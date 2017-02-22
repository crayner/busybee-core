<?php

namespace Busybee\PersonBundle\Form;

use Busybee\FormBundle\Type\YesNoType;
use Busybee\PersonBundle\Entity\Person;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
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
            ->add('directroles', EntityType::class,
                array(
					'label' 				=> 'user.label.directroles',
					'multiple' 				=> true,
					'expanded' 				=> true,
					'class' 				=> 'Busybee\SecurityBundle\Entity\Role',
					'choice_label' 			=> 'role',
					'required' 				=> false,
					'attr'					=> array(
						'help' 					=> 'user.help.directroles',
                        'class'                 => 'user',
                    ),
				)
			)
            ->add('groups', EntityType::class,
                array(
                    'multiple' 				=> true,
                    'expanded' 				=> true,
                    'class' 				=> 'Busybee\SecurityBundle\Entity\Group',
                    'choice_label' 			=> 'groupname',
                    'label' 				=> 'user.label.groups',
                    'required' 				=> false,
                    'attr'					=> array(
                        'help' 					=> 'user.help.groups',
                        'class'                 => 'user',
                    ),
                )
            )
            ->add('enabled', YesNoType::class,
                array(
                    'label' 				=> 'user.label.enabled',
                    'attr'					=> array(
                        'help' 					=> 'user.help.enabled',
                        'class'                 => 'user',
                        'data-off-icon-cls'	 	=> "halflings-thumbs-down",
                        'data-on-icon-cls' 		=> "halflings-thumbs-up",
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
            ->add('locked', YesNoType::class,
                array(
                    'label' 				=> 'user.label.locked',
                    'attr'					=> array(
                        'help' 					=> 'user.help.locked',
                        'class'                 => 'user',
                        'data-off-icon-cls'	 	=> "halflings-thumbs-down",
                        'data-on-icon-cls' 		=> "halflings-thumbs-up",
                    ),
                )
            )
            ->add('expired', YesNoType::class,
                array(
                    'label' 				=> 'user.label.expired',
                    'attr'					=> array(
                        'help' 					=> 'user.help.expired',
                        'class'                 => 'user',
                        'data-off-icon-cls'	 	=> "halflings-thumbs-down",
                        'data-on-icon-cls' 		=> "halflings-thumbs-up",
                    ),
              )
            )
            ->add('credentials_expired', YesNoType::class,
                array(
                    'label' 				=> 'user.label.credentials_expired',
                    'attr'					=> array(
                        'help' 					=> 'user.help.credentials_expired',
                        'class'                 => 'user',
                        'data-off-icon-cls'	 	=> "halflings-thumbs-down",
                        'data-on-icon-cls' 		=> "halflings-thumbs-up",
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
        ;
        $builder->get('person')->addModelTransformer(new EntityToStringTransformer($this->manager, Person::class));
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
