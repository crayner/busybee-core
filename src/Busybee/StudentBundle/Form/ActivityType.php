<?php

namespace Busybee\StudentBundle\Form;

use Busybee\FormBundle\Type\SettingChoiceType;
use Busybee\InstituteBundle\Entity\Year;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\StaffBundle\Entity\Staff;
use Busybee\StudentBundle\Entity\Activity;
use Busybee\StudentBundle\Entity\Student;
use Busybee\StudentBundle\Events\ActivitySubscriber;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * ActivityType constructor.
     * @param   ObjectManager $om
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
                    'label' => 'activity.label.name'
                ]
            )
            ->add('nameShort', null,
                [
                    'label' => 'activity.label.nameShort'
                ]
            )
            ->add('year', EntityType::class,
                [
                    'label' => 'activity.label.year',
                    'class' => Year::class,
                    'choice_label' => 'name',
                    'placeholder' => 'activity.placeholder.year',
                    'required' => true,
                ]
            )
            ->add('grades', SettingChoiceType::class,
                [
                    'label' => 'activity.label.grades',
                    'placeholder' => 'activity.placeholder.grades',
                    'setting_name' => 'student.groups',
                    'multiple' => true,
                    'attr' => [
                        'help' => 'activity.help.grades',
                    ],
                ]
            )
            ->add('tutor1', EntityType::class,
                [
                    'label' => 'activity.label.tutor1',
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'placeholder' => 'activity.placeholder.tutor',
                    'attr' => [
                        'help' => 'activity.help.tutor'
                    ],
                    'required' => true,
                ]
            )
            ->add('tutor2', EntityType::class,
                [
                    'label' => 'activity.label.tutor2',
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'placeholder' => 'activity.placeholder.tutor',
                    'attr' => [
                        'help' => 'activity.help.tutor'
                    ],
                    'required' => false,
                ]
            )
            ->add('tutor3', EntityType::class,
                [
                    'label' => 'activity.label.tutor3',
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'placeholder' => 'activity.placeholder.tutor',
                    'attr' => [
                        'help' => 'activity.help.tutor'
                    ],
                    'required' => false,
                ]
            );

        $builder->get('year')->addModelTransformer(new EntityToStringTransformer($this->om, Year::class));

        $builder->addEventSubscriber(new ActivitySubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Activity::class,
                'translation_domain' => 'BusybeeStudentBundle',
                'year_data' => null,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'activity';
    }


}
