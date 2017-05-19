<?php

namespace Busybee\TimeTableBundle\Form;

use Busybee\FormBundle\Type\TimeType;
use Busybee\FormBundle\Type\ToggleType;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\SystemBundle\Setting\SettingManager;
use Busybee\TimeTableBundle\Entity\Day;
use Busybee\TimeTableBundle\Entity\TimeTable;
use Busybee\TimeTableBundle\Events\DaySubscriber;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DayEntityType extends AbstractType
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
                'data_class' => Day::class,
                'translation_domain' => 'BusybeeTimeTableBundle',
                'placeholder' => 'timetable.day.placeholder',
                'class' => Day::class,
                'choice_label' => 'name',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tt_days';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null,
                [
                    'label' => 'timetable.day.name.label',
                ]
            )
            ->add('dayType', ToggleType::class,
                [
                    'label' => 'timetable.day.dayType.label',
                    'attr' =>
                        [
                            'help' => 'timetable.day.dayType.help',
                            'data-off' => 'timetable.dayType.fixed',
                            'data-on' => 'timetable.dayType.rotate',
                        ],

                ]
            )
            ->add('timetable', HiddenType::class);

        $builder->get('timetable')->addModelTransformer(new EntityToStringTransformer($this->om, TimeTable::class));

    }
}
