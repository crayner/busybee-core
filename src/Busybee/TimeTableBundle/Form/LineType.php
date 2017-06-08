<?php

namespace Busybee\TimeTableBundle\Form;

use Busybee\CurriculumBundle\Entity\Course;
use Busybee\FormBundle\Type\ToggleType;
use Busybee\InstituteBundle\Form\YearEntityType;
use Busybee\TimeTableBundle\Entity\Line;
use Busybee\TimeTableBundle\Events\LineSubscriber;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LineType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $year = $options['year_data'];
        $builder
            ->add('name', null, [
                    'label' => 'line.label.name',
                ]
            )
            ->add('nameShort', null, [
                    'label' => 'line.label.nameShort',
                ]
            )
            ->add('participants', NumberType::class, [
                    'label' => 'line.label.participants',
                    'attr' => [
                        'help' => 'line.help.participants',
                    ],
                ]
            )
            ->add('includeAll', ToggleType::class, [
                    'label' => 'line.label.includeAll',
                    'attr' => [
                        'help' => 'line.help.includeAll',
                    ],
                ]
            )
            ->add('course', EntityType::class, [
                    'class' => Course::class,
                    'choice_label' => 'name',
                    'placeholder' => 'line.placeholder.course',
                    'label' => 'line.label.course',
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');
                    },
                ]
            )
            ->add('year', YearEntityType::class, [
                    'placeholder' => 'line.placeholder.year',
                    'label' => 'line.label.year',
                ]
            )
            ->add('changeRecord', EntityType::class,
                array(
                    'label' => false,
                    'attr' => array(
                        'class' => 'formChanged changeRecord',
                    ),
                    'class' => line::class,
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
                    'placeholder' => 'line.placeholder.changeRecord',
                )
            );
        $builder->addEventSubscriber(new lineSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => line::class,
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
        return 'line';
    }


}
