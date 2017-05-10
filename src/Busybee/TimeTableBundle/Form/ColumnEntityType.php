<?php

namespace Busybee\TimeTableBundle\Form;

use Busybee\FormBundle\Type\ToggleType;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\TimeTableBundle\Entity\Column;
use Busybee\TimeTableBundle\Entity\Day;
use Busybee\TimeTableBundle\Entity\TimeTable;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
        $resolver->setRequired(
            [
                'timetable_id',
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
        $choices = ['Rotate' => 'Rotate'];
        $days = $this->om->getRepository(Day::class)->createQueryBuilder('d')
            ->leftJoin('d.timetable', 't')
            ->select('d.name')
            ->where('t.id = :tt_id')
            ->setParameter('tt_id', $options['timetable_id'])
            ->andWhere('d.dayType = :false')
            ->setParameter('false', false)
            ->getQuery()
            ->getResult();

        foreach ($days as $day)
            $choices[$day['name']] = $day['name'];

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
            ->add('mappingInfo', ChoiceType::class,
                [
                    'label' => 'timetable.column.mappingInfo.label',
                    'attr' =>
                        [
                            'help' => 'timetable.column.mappingInfo.help',
                        ],
                    'choices' => $choices,
                    'empty_data' => 'Rotate',
                ]
            )
            ->add('timetable', HiddenType::class);

        $builder->get('timetable')->addModelTransformer(new EntityToStringTransformer($this->om, TimeTable::class));
    }
}
