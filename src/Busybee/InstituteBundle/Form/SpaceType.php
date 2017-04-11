<?php

namespace Busybee\InstituteBundle\Form;

use Busybee\FormBundle\Type\ToggleType;
use Busybee\InstituteBundle\Entity\Space;
use Busybee\InstituteBundle\Form\DataTransformer\CampusTransformer;
use Busybee\StaffBundle\Form\DataTransformer\StaffTransformer;
use Busybee\SystemBundle\Setting\SettingManager;
use Busybee\StaffBundle\Entity\Staff;
use Symfony\Component\Form\Extension\Core\Type\IntegerType ;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\Common\Persistence\ObjectManager ;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpaceType extends AbstractType
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
                    'label' => 'campus.space.label.name',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('type', ChoiceType::class, array(
                    'choices' => $this->sm->get('Campus.Space.Type'),
                    'label' => 'campus.space.label.type',
                    'placeholder' => 'campus.space.placeholder.type',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('capacity', IntegerType::class, array(
                    'label' => 'campus.space.label.capacity',
                    'attr' => array(
                        'min' => '0',
                        'max' => '9999',
                        'help' => 'campus.space.help.capacity',
                        'class' => 'monitorChange',
                    ),
                    'empty_data' => 0,
                )
            )
            ->add('computer', ToggleType::class, array(
                    'label' => 'campus.space.label.computer',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('studentComputers', IntegerType::class, array(
                    'label' => 'campus.space.label.studentComputers',
                    'attr' => array(
                        'min' => '0',
                        'max' => '999',
                        'class' => 'monitorChange',
                    ),
                    'empty_data' => 0,
                )
            )
            ->add('projector', ToggleType::class, array(
                    'label' => 'campus.space.label.projector',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('tv', ToggleType::class, array(
                    'label' => 'campus.space.label.tv',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('dvd', ToggleType::class, array(
                    'label' => 'campus.space.label.dvd',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('hifi', ToggleType::class, array(
                    'label' => 'campus.space.label.hifi',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('speakers', ToggleType::class, array(
                    'label' => 'campus.space.label.speakers',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('iwb', ToggleType::class, array(
                    'label' => 'campus.space.label.iwb',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('phoneInt', null, array(
                    'label' => 'campus.space.label.phoneint',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('phoneExt', null, array(
                    'label' => 'campus.space.label.phoneext',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('comment', TextareaType::class, array(
                    'label' => 'campus.space.label.comment',
                    'required' => false,
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('staff1', EntityType::class,
                array(
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'choice_value' => 'id',
                    'label' => 'campus.space.label.staff1',
                    'placeholder' => 'campus.space.placeholder.staff',
                    'empty_data'    => null,
                    'attr' => array(
                        'help' => 'campus.space.help.staff',
                        'class' => 'monitorChange',
                    ),
                    'required' => false,
                )
            )
            ->add('campus', EntityType::class,
                array(
                    'label' => 'campus.space.label.campus',
                    'attr' => array(
                        'help' => 'campus.space.help.campus',
                        'class' => 'monitorChange',
                    ),
                    'class' => 'Busybee\InstituteBundle\Entity\Campus',
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'empty_data' => $this->manager->getRepository('BusybeeInstituteBundle:Campus')->find(1),
                    'placeholder' => 'campus.space.placeholder.campus',
                )
            )
            ->add('staff2', EntityType::class,
                array(
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'choice_value' => 'id',
                    'label' => 'campus.space.label.staff2',
                    'placeholder' => 'campus.space.placeholder.staff',
                    'empty_data'    => null,
                    'attr' => array(
                        'help' => 'campus.space.help.staff',
                        'class' => 'monitorChange',
                    ),
                    'required' => false,
                )
            )
            ->add('changeRecord', EntityType::class,
                array(
                    'label' => false,
                    'attr' => array(
                        'class' => 'formChanged changeRecord',
                    ),
                    'class' => Space::class,
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'mapped' => false,
                    'required' => false,
                    'placeholder' => 'campus.space.placeholder.changeRecord',
                )
            )
        ;

        $builder->get('campus')->addModelTransformer(new CampusTransformer($this->manager));
        $builder->get('staff1')->addModelTransformer(new StaffTransformer($this->manager));
        $builder->get('staff2')->addModelTransformer(new StaffTransformer($this->manager));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Space::class,
            'translation_domain' => 'BusybeeInstituteBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'space';
    }


}
