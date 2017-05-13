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
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
        $builder
            ->add('name', null,
                [
                    'label' => 'timetable.label.name',
                ]
            )
            ->add('nameShort', null,
                [
                    'label' => 'timetable.label.nameShort',
                ]
            )
            ->add('year', YearEntityType::class,
                [
                    'label' => 'timetable.label.year',
                    'placeholder' => 'timetable.placeholder.year',
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
                ]
            );

        $builder->addEventSubscriber(new TimeTableSubscriber($this->sm));
        $builder->get('year')->addModelTransformer(new EntityToStringTransformer($this->om, Year::class));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TimeTable::class,
            'translation_domain' => "BusybeeTimeTableBundle",
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timetable';
    }


}
