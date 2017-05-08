<?php

namespace Busybee\TimeTableBundle\Form;

use Busybee\CurriculumBundle\Entity\Course;
use Busybee\FormBundle\Type\ToggleType;
use Busybee\InstituteBundle\Form\YearEntityType;
use Busybee\TimeTableBundle\Entity\ActivityGroups;
use Busybee\TimeTableBundle\Events\ActivityGroupsSubscriber;
use Busybee\TimeTableBundle\Events\ActivityGroupSubscriber;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityGroupsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $year = $options['year_data'];
        $builder
            ->add('name', null, [
                    'label' => 'activitygroups.label.name',
                ]
            )
            ->add('nameShort', null, [
                    'label' => 'activitygroups.label.nameShort',
                ]
            )
            ->add('participants', NumberType::class, [
                    'label' => 'activitygroups.label.participants',
                    'attr' => [
                        'help' => 'activitygroups.help.participants',
                    ],
                ]
            )
            ->add('includeAll', ToggleType::class, [
                    'label' => 'activitygroups.label.includeAll',
                    'attr' => [
                        'help' => 'activitygroups.help.includeAll',
                    ],
                ]
            )
            ->add('course', EntityType::class, [
                    'class' => Course::class,
                    'choice_label' => 'name',
                    'placeholder' => 'activitygroups.placeholder.course',
                    'label' => 'activitygroups.label.course',
                    'required' => false,
                ]
            )
            ->add('year', YearEntityType::class, [
                    'placeholder' => 'activitygroups.placeholder.year',
                    'label' => 'activitygroups.label.year',
                ]
            )
            ->add('changeRecord', EntityType::class,
                array(
                    'label' => false,
                    'attr' => array(
                        'class' => 'formChanged changeRecord',
                    ),
                    'class' => ActivityGroups::class,
                    'choice_label' => 'name',
                    'mapped' => false,
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) use ($year) {
                        return $er->createQueryBuilder('l')
                            ->leftJoin('l.year', 'y')
                            ->where('y.id = :year_id')
                            ->setParameter('year_id', $year->getId())
                            ->orderBy('l.name', 'ASC');
                    },
                    'placeholder' => 'activitygroups.placeholder.changeRecord',
                )
            );
        $builder->addEventSubscriber(new ActivityGroupsSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => ActivityGroups::class,
                'translation_domain' => 'BusybeeTimeTableBundle',
            ]
        );
        $resolver->setRequired(
            [
                'year_data',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'activity_groups';
    }


}
