<?php

namespace Busybee\TimeTableBundle\Form;

use Busybee\FormBundle\Type\ToggleType;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\TimeTableBundle\Entity\Column;
use Busybee\TimeTableBundle\Entity\TimeTable;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColumnEntityType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * TimeTableType constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Column::class,
                'translation_domain' => 'BusybeeTimeTableBundle',
                'placeholder' => 'timetable.column.placeholder',
                'class' => Column::class,
                'choice_label' => 'name',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tt_columns';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null,
                [
                    'label' => 'timetable.column.name.label',
                ]
            )
            ->add('nameShort', null,
                [
                    'label' => 'timetable.column.nameShort.label',
                ]
            )
            ->add('dayType', ToggleType::class,
                [
                    'label' => 'timetable.column.dayType.label',
                    'attr' =>
                        [
                            'help' => 'timetable.column.dayType.help',
                            'data-off' => 'timetable.dayType.fixed',
                            'data-on' => 'timetable.dayType.rotate',
                        ],
                ]
            )
            ->add('timetable', HiddenType::class);

        $builder->get('timetable')->addModelTransformer(new EntityToStringTransformer($this->om, TimeTable::class));
    }
}
