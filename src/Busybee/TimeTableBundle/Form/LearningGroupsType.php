<?php

namespace Busybee\TimeTableBundle\Form;

use Busybee\CurriculumBundle\Entity\Course;
use Busybee\FormBundle\Type\ToggleType;
use Busybee\InstituteBundle\Form\YearEntityType;
use Busybee\TimeTableBundle\Entity\LearningGroups;
use Busybee\TimeTableBundle\Events\LearningGroupsSubscriber;
use Busybee\TimeTableBundle\Events\LearningGroupSubscriber;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LearningGroupsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $year = $options['year_data'];
        $builder
            ->add('name', null, [
                    'label' => 'learninggroups.label.name',
                ]
            )
            ->add('nameShort', null, [
                    'label' => 'learninggroups.label.nameShort',
                ]
            )
            ->add('participants', NumberType::class, [
                    'label' => 'learninggroups.label.participants',
                    'attr' => [
                        'help' => 'learninggroups.help.participants',
                    ],
                ]
            )
            ->add('includeAll', ToggleType::class, [
                    'label' => 'learninggroups.label.includeAll',
                    'attr' => [
                        'help' => 'learninggroups.help.includeAll',
                    ],
                ]
            )
            ->add('course', EntityType::class, [
                    'class' => Course::class,
                    'choice_label' => 'name',
                    'placeholder' => 'learninggroups.placeholder.course',
                    'label' => 'learninggroups.label.course',
                    'required' => false,
                ]
            )
            ->add('year', YearEntityType::class, [
                    'placeholder' => 'learninggroups.placeholder.year',
                    'label' => 'learninggroups.label.year',
                ]
            )
            ->add('changeRecord', EntityType::class,
                array(
                    'label' => false,
                    'attr' => array(
                        'class' => 'formChanged changeRecord',
                    ),
                    'class' => LearningGroups::class,
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
                    'placeholder' => 'learninggroups.placeholder.changeRecord',
                )
            );
        $builder->addEventSubscriber(new LearningGroupsSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => LearningGroups::class,
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
        return 'learning_groups';
    }


}
