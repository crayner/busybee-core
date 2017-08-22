<?php

namespace Busybee\StudentBundle\Form;

use Busybee\Core\FormBundle\Type\SettingChoiceType;
use Busybee\Core\CalendarBundle\Entity\Grade;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\StudentBundle\Entity\Student;
use Busybee\StudentBundle\Entity\StudentGrade;
use Busybee\StudentBundle\Events\StudentGradesSubscriber;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentGradeType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * StaffType constructor.
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
            ->add('status', SettingChoiceType::class,
                [
                    'setting_name' => 'student.enrolment.status',
                    'label' => 'grades.label.status',
                    'placeholder' => 'grades.placeholder.status',
                    'attr' => [
                        'help' => 'grades.help.status',
                    ],
                ]
            )
            ->add('student', HiddenType::class)
            ->add('grade', EntityType::class,
                [
                    'class' => Grade::class,
                    'choice_label' => 'gradeYear',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('g')
                            ->orderBy('g.year', 'DESC')
                            ->addOrderBy('g.sequence', 'ASC');
                    },
                    'placeholder' => 'grades.placeholder.grade',
                    'label' => 'grades.label.grade',
                    'attr' => [
                        'help' => 'grades.help.grade',
                    ],
                ]
            );

        $builder->get('student')->addModelTransformer(new EntityToStringTransformer($this->om, Student::class));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'data_class' => StudentGrade::class,
                    'translation_domain' => 'BusybeeStudentBundle',
                    'year_data' => null,
                    'error_bubbling' => true,
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'studentGrade';
    }


}
