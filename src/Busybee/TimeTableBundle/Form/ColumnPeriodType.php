<?php

namespace Busybee\TimeTableBundle\Form;

use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\TimeTableBundle\Entity\Column;
use Busybee\TimeTableBundle\Entity\TimeTable;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColumnPeriodType extends AbstractType
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
                'class' => Column::class,
            ]
        );
        $resolver->setRequired(
            [
                'tt_id',
            ]
        );

    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'column_period';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', HiddenType::class)
            ->add('nameShort', HiddenType::class)
            ->add('mappingInfo', HiddenType::class)
            ->add('periods', CollectionType::class,
                [
                    'entry_type' => PeriodType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            )
            ->add('timetable', HiddenType::class);
        $builder->get('timetable')->addModelTransformer(new EntityToStringTransformer($this->om, TimeTable::class));
    }
}
