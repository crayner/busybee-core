<?php

namespace Busybee\InstituteBundle\Form;

use Busybee\Core\TemplateBundle\Type\ToggleType;
use Busybee\InstituteBundle\Entity\Space;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Busybee\People\StaffBundle\Entity\Staff;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
        $person_id = empty($options['data']->getStaff()) ? null : $options['data']->getStaff()->getPerson()->getId();
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
                    'empty_data' => '0',
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
            ->add('duplicateid', HiddenType::class, [
                    'mapped' => false,
                ]
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
            ->add('staff', EntityType::class,
                array(
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'choice_value' => 'id',
                    'label' => 'campus.space.label.staff',
                    'placeholder' => 'campus.space.placeholder.staff',
                    'empty_data'    => null,
                    'attr' => array(
                        'help' => 'campus.space.help.staff',
                        'class' => 'monitorChange',
                    ),
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) use ($person_id) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.homeroom', 'h')
                            ->leftJoin('s.person', 'p')
                            ->orderBy('p.surname', 'ASC')
                            ->addOrderBy('p.firstName', 'ASC')
                            ->where('s.person = :person_id')
                            ->setParameter('person_id', $person_id)
                            ->orWhere('h.staff IS NULL');
                    },
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
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->orderBy('s.name', 'ASC');
                    },
                )
            )
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Space::class,
                'translation_domain' => 'BusybeeInstituteBundle',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'space';
    }


}
