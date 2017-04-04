<?php

namespace Busybee\StudentBundle\Form;

use Busybee\CurriculumBundle\Entity\Course;
use Busybee\InstituteBundle\Entity\Year;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\StudentBundle\Entity\Activity;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ->add('name')
            ->add('nameShort')
            ->add('year', EntityType::class,
                array(
                    'label' => 'activity.label.year',
                    'class' => Year::class,
                    'choice_label' => 'name',
                    'placeholder' => 'activity.placeholder.year',
                )
            )
            ->add('course', EntityType::class,
                array(
                    'label' => 'activity.label.course',
                    'class' => Course::class,
                    'choice_label' => 'name',
                    'placeholder' => 'activity.placeholder.course',
                )
            );

        $builder->get('year')->addModelTransformer(new EntityToStringTransformer($this->om, Year::class));
        $builder->get('course')->addModelTransformer(new EntityToStringTransformer($this->om, Course::class));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Activity::class,
            'translation_domain' => 'BusybeeStudentBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'activity';
    }


}
