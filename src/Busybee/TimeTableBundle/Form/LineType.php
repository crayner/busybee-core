<?php

namespace Busybee\TimeTableBundle\Form;

use Busybee\CurriculumBundle\Entity\Course;
use Busybee\FormBundle\Type\ToggleType;
use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\TimeTableBundle\Entity\Line;
use Busybee\TimeTableBundle\Events\LineSubscriber;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LineType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * LineType constructor.
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
        $builder
            ->add('name', null, [
                    'label' => 'line.label.name',
                    'attr' => [
                        'class' => 'monitorChange',
                    ],
                ]
            )
            ->add('nameShort', null, [
                    'label' => 'line.label.nameShort',
                    'attr' => [
                        'class' => 'monitorChange',
                    ],
                ]
            )
            ->add('participants', NumberType::class, [
                    'label' => 'line.label.participants',
                    'attr' => [
                        'help' => 'line.help.participants',
                        'class' => 'monitorChange',
                    ],
                ]
            )
            ->add('includeAll', ToggleType::class, [
                    'label' => 'line.label.includeAll',
                    'attr' => [
                        'help' => 'line.help.includeAll',
                        'class' => 'monitorChange',
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
                    'attr' => [
                        'class' => 'monitorChange',
                    ],
                ]
            )
            ->add('year', HiddenType::class)
            ->add('changeRecord', EntityType::class,
                array(
                    'label' => false,
                    'attr' => array(
                        'class' => 'formChanged changeRecord',
                    ),
                    'class' => Line::class,
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

        $builder->get('year')->addModelTransformer(new EntityToStringTransformer($this->om, Year::class));
        $builder->addEventSubscriber(new lineSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Line::class,
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
