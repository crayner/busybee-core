<?php

namespace Busybee\TimeTableBundle\Form;

use Busybee\InstituteBundle\Entity\Space;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\StaffBundle\Entity\Staff;
use Busybee\ActivityBundle\Entity\Activity;
use Busybee\TimeTableBundle\Entity\Period;
use Busybee\TimeTableBundle\Entity\PeriodActivity;
use Busybee\TimeTableBundle\Events\PeriodActivitySubscriber;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditPeriodActivityType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * EditPeriodActivityType constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $year = $options['year_data'];
        $inSpace = is_null($options['data']->getInheritedSpace()) ? 'No Inheritance' : $options['data']->getInheritedSpace()->getNameCapacity();
        $inTutor1 = is_null($options['data']->getInheritedTutor1()) ? 'No Inheritance' : $options['data']->getInheritedTutor1()->getFullName();
        $inTutor2 = is_null($options['data']->getInheritedTutor2()) ? 'No Inheritance' : $options['data']->getInheritedTutor2()->getFullName();
        $inTutor3 = is_null($options['data']->getInheritedTutor3()) ? 'No Inheritance' : $options['data']->getInheritedTutor3()->getFullName();
        $builder
            ->add('activity', HiddenType::class)
            ->add('period', HiddenType::class)
            ->add('space', EntityType::class, [
                    'class' => Space::class,
                    'choice_label' => 'nameCapacity',
                    'placeholder' => 'space.placeholder',
                    'translation_domain' => 'BusybeeInstituteBundle',
                    'query_builder' => function (EntityRepository $er) use ($year) {
                        return $er->createQueryBuilder('s')
                            ->orderby('s.name');
                    },
                    'attr' => [
                        'help' => ['space.help', ['%space%' => $inSpace]],
                    ],
                    'required' => false,
                ]
            )
            ->add('tutor1', EntityType::class, [
                    'class' => Staff::class,
                    'choice_label' => 'fullName',
                    'placeholder' => 'tutor.placeholder',
                    'translation_domain' => 'BusybeeStaffBundle',
                    'query_builder' => function (EntityRepository $er) use ($year) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.person', 'p')
                            ->orderby('p.surname', 'ASC')
                            ->addOrderby('p.firstName', 'ASC');
                    },
                    'attr' => [
                        'help' => ['tutor.help.tutor1', ['%name%' => $inTutor1]],
                    ],
                    'required' => false,
                ]
            )
            ->add('tutor2', EntityType::class, [
                    'class' => Staff::class,
                    'choice_label' => 'fullName',
                    'placeholder' => 'tutor.placeholder',
                    'translation_domain' => 'BusybeeStaffBundle',
                    'query_builder' => function (EntityRepository $er) use ($year) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.person', 'p')
                            ->orderby('p.surname', 'ASC')
                            ->addOrderby('p.firstName', 'ASC');
                    },
                    'attr' => [
                        'help' => ['tutor.help.tutor', ['%name%' => $inTutor2]],
                    ],
                    'required' => false,
                ]
            )
            ->add('tutor3', EntityType::class, [
                    'class' => Staff::class,
                    'choice_label' => 'fullName',
                    'placeholder' => 'tutor.placeholder',
                    'translation_domain' => 'BusybeeStaffBundle',
                    'query_builder' => function (EntityRepository $er) use ($year) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.person', 'p')
                            ->orderby('p.surname', 'ASC')
                            ->addOrderby('p.firstName', 'ASC');
                    },
                    'attr' => [
                        'help' => ['tutor.help.tutor', ['%name%' => $inTutor3]],
                    ],
                    'required' => false,
                ]
            );

        $builder->get('activity')->addModelTransformer(new EntityToStringTransformer($this->om, Activity::class));
        $builder->get('period')->addModelTransformer(new EntityToStringTransformer($this->om, Period::class));

        $builder->addEventSubscriber(new PeriodActivitySubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => PeriodActivity::class,
                'translation_domain' => 'BusybeeTimeTableBundle',
                'error_bubbling' => true,
            ]
        );
        $resolver->setRequired([
            'year_data',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tt_period_activity';
    }
}
