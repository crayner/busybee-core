<?php

namespace Busybee\TimeTableBundle\Form;

use Busybee\InstituteBundle\Entity\Year;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\TimeTableBundle\Entity\TimeTable;
use Busybee\TimeTableBundle\Events\TimeTableSubscriber;
use Doctrine\Common\Persistence\ObjectManager;
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
            )
            ->add('lines', CollectionType::class,
                [
                    'label' => 'timetable.label.lines',
                    'entry_type' => LineType::class,
                    'allow_add' => true,
                    'by_reference' => false,
                    'allow_delete' => true,
                    'prototype_name' => '__linename__',
                    'attr' => array(
                        'class' => 'lineList',
                        'help' => 'timetable.help.lines',
                    ),
                    'required' => false,
                ]
            );

        $builder->get('year')->addModelTransformer(new EntityToStringTransformer($this->om, Year::class));
        $builder->addEventSubscriber(new TimeTableSubscriber());
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
