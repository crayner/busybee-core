<?php

namespace Busybee\CurriculumBundle\Form;

use Busybee\CurriculumBundle\Entity\Course;
use Busybee\FormBundle\Type\TextType;
use Busybee\InstituteBundle\Entity\StudentYear;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * CourseType constructor.
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
                array(
                    'label' => 'course.label.name',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('version', TextType::class,
                array(
                    'label' => 'course.label.version',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('studentYear', EntityType::class,
                array(
                    'label' => 'course.label.studentYear',
                    'class' => StudentYear::class,
                    'choice_label' => 'name',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->addOrderBy('s.year', 'ASC')
                            ->addOrderBy('s.sequence', 'ASC');
                    },
                    'placeholder' => 'course.placeholder.studentYear',
                )
            )
            ->add('changeRecord', EntityType::class,
                array(
                    'label' => false,
                    'attr' => array(
                        'class' => 'formChanged changeRecord',
                    ),
                    'class' => Course::class,
                    'choice_label' => 'studentYearName',
                    'choice_value' => 'id',
                    'mapped' => false,
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->leftJoin('c.studentYear', 'y')
                            ->addOrderBy('c.name', 'ASC')
                            ->addOrderBy('y.sequence', 'ASC');
                    },
                    'placeholder' => 'course.placeholder.changeRecord',
                )
            );
        $builder->get('studentYear')->addModelTransformer(new EntityToStringTransformer($this->manager, StudentYear::class));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Course::class,
                'translation_domain' => 'BusybeeCurriculumBundle',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'course';
    }


}
