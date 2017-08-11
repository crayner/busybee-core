<?php

namespace Busybee\TimeTableBundle\Form;

use Busybee\FormBundle\Type\ToggleType;
use Busybee\InstituteBundle\Entity\Year;
use Busybee\InstituteBundle\Form\YearEntityType;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\SystemBundle\Setting\SettingManager;
use Busybee\TimeTableBundle\Entity\TimeTable;
use Busybee\TimeTableBundle\Events\TimeTableSubscriber;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimeTableType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var SettingManager
     */
    private $sm;

    /**
     * TimeTableType constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, SettingManager $sm)
    {
        $this->om = $om;
        $this->sm = $sm;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locked = $options['locked'];
        $builder
            ->add('name', null,
                [
                    'label' => 'timetable.label.name',
                    'disabled' => $locked,
                ]
            )
            ->add('nameShort', null,
                [
                    'label' => 'timetable.label.nameShort',
                    'disabled' => $locked,
                ]
            )
            ->add('year', YearEntityType::class,
                [
                    'label' => 'timetable.label.year',
                    'placeholder' => 'timetable.placeholder.year',
                    'disabled' => $locked,
                ]
            )
            ->add('locked', ToggleType::class,
                [
                    'label' => 'timetable.locked.label',
                    'attr' => [
                        'help' => 'timetable.locked.help',
                    ],
                    'disabled' => $locked,
                ]
            )
            ->add('columns', CollectionType::class,
                [
                    'entry_type' => ColumnEntityType::class,
                    'attr' =>
                        [
                            'class' => 'columnList',
                            'help' => 'timetable.columns.help',
                        ],
                    'label' => 'timetable.columns.label',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'entry_options' =>
                        [
                            'timetable_id' => $options['data']->getId(),
                        ],
                    'disabled' => $locked,
                ]
            )
            ->add('days', CollectionType::class,
                [
                    'entry_type' => DayEntityType::class,
                    'attr' =>
                        [
                            'class' => 'dayList',
                            'help' => 'timetable.days.help',
                        ],
                    'label' => 'timetable.days.label',
                    'allow_delete' => false,
                    'allow_add' => false,
                    'disabled' => $locked,
                ]
            );

        $builder->addEventSubscriber(new TimeTableSubscriber($this->sm, $this->om));
        $builder->get('year')->addModelTransformer(new EntityToStringTransformer($this->om, Year::class));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => TimeTable::class,
                'translation_domain' => "BusybeeTimeTableBundle",
            ]
        );
        $resolver->setRequired(
            [
                'locked',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timetable_days';
    }


}
