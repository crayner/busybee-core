<?php

namespace Busybee\TimeTableBundle\Form;

use Busybee\InstituteBundle\Entity\Year;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\TimeTableBundle\Entity\TimeTable;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimeTableType extends AbstractType
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
            ->add('year', EntityType::class,
                [
                    'class' => Year::class,
                    'label' => 'timetable.label.year',
                    'choice_label' => 'name',
                    'placeholder' => 'timetable.placeholder.year'
                ]
            );

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
