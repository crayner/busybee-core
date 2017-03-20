<?php

namespace Busybee\InstituteBundle\Form;

use Busybee\InstituteBundle\Entity\CampusResource;
use Busybee\InstituteBundle\Entity\HomeRoom;
use Busybee\InstituteBundle\Entity\StudentYear;
use Busybee\InstituteBundle\Events\TutorSubscriber;
use Busybee\InstituteBundle\Form\DataTransformer\CampusResourceTransformer;
use Busybee\InstituteBundle\Form\DataTransformer\YearTransformer;
use Busybee\StaffBundle\Entity\Staff;
use Busybee\StaffBundle\Form\DataTransformer\StaffTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Busybee\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Persistence\ObjectManager ;

class HomeRoomType extends AbstractType
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
     * Construct
     */
    public function __construct(ObjectManager $manager, SettingManager $sm)
    {
        $this->manager = $manager ;
        $this->sm = $sm;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                    'attr' => array(
                        'class' => 'monitorChange',
                        'help' => 'homeroom.help.name',
                    ),
                    'label' => 'homeroom.label.name',
                )
            )
            ->add('nameShort', null, array(
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                    'label' => 'homeroom.label.nameShort',
                )
            )
            ->add('website', null, array(
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                    'label' => 'homeroom.label.website',
                    'trim' => true,
                )
            )
            ->add('schoolYear', EntityType::class, array(
                    'choice_label' => 'name',
                    'class' => StudentYear::class,
                    'placeholder' => 'homeroom.placeholder.schoolYear',
                    'label' => 'homeroom.label.schoolYear',
                    'attr' => array(
                        'help' => 'homeroom.help.schoolYear',
                        'class' => 'monitorChange',
                    ),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.year', 'y')
                            ->where('y.status = :current')
                            ->setParameter('current', 'current')
                            ->orderby('y.sequence');
                    },
                )
            )
            ->add('tutor1', EntityType::class, array(
                    'choice_label' => 'formatName',
                    'class' => Staff::class,
                    'required' => false,
                    'placeholder' => 'homeroom.placeholder.tutor',
                    'attr' => array(
                        'class' => 'monitorChange',
                        'help' => 'homeroom.help.tutor1',
                    ),
                    'label' => 'homeroom.label.tutor.1'
               )
            )
            ->add('tutor2', EntityType::class, array(
                    'choice_label' => 'formatName',
                    'class' => Staff::class,
                    'required' => false,
                    'placeholder' => 'homeroom.placeholder.tutor',
                    'attr' => array(
                        'class' => 'monitorChange',
                        'help' => 'homeroom.help.tutorAlt',
                    ),
                    'label' => 'homeroom.label.tutor.2'
                )
            )
            ->add('tutor3', EntityType::class, array(
                    'choice_label' => 'formatName',
                    'class' => Staff::class,
                    'required' => false,
                    'placeholder' => 'homeroom.placeholder.tutor',
                    'attr' => array(
                        'class' => 'monitorChange',
                        'help' => 'homeroom.help.tutorAlt',
                    ),
                    'label' => 'homeroom.label.tutor.3'
                )
            )
            ->add('campusResource', EntityType::class, array(
                    'choice_label' => 'name',
                    'class' => CampusResource::class,
                    'placeholder' => 'homeroom.placeholder.campusResource',
                    'label' => 'homeroom.label.campusResource',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('changeRecord', EntityType::class,
                array(
                    'label' => false,
                    'attr' => array(
                        'class' => 'formChanged changeRecord',
                    ),
                    'class' => HomeRoom::class,
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'mapped' => false,
                    'required' => false,
                    'placeholder' => 'homeroom.placeholder.changeRecord',
                )
            )
        ;
        $builder->get('schoolYear')->addModelTransformer(new YearTransformer($this->manager));
        $builder->get('tutor1')->addModelTransformer(new StaffTransformer($this->manager));
        $builder->get('tutor2')->addModelTransformer(new StaffTransformer($this->manager));
        $builder->get('tutor3')->addModelTransformer(new StaffTransformer($this->manager));
        $builder->get('campusResource')->addModelTransformer(new CampusResourceTransformer($this->manager));

        $builder->addEventSubscriber(new TutorSubscriber($this->manager->getRepository('BusybeeInstituteBundle:CampusResource')));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Busybee\InstituteBundle\Entity\HomeRoom',
                'translation_domain' => 'BusybeeInstituteBundle',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'homeroom';
    }


}
