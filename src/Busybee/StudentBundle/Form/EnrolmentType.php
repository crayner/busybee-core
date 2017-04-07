<?php

namespace Busybee\StudentBundle\Form;

use Busybee\FormBundle\Type\SettingChoiceType;
use Busybee\InstituteBundle\Entity\Year;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\StudentBundle\Entity\Student;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnrolmentType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * EnrolmentType constructor.
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
                    'label' => 'student.enrolment.label.status',
                    'setting_name' => 'student.enrolment.status',
                    'placeholder' => 'student.enrolment.placeholder.status',
                ]
            )
            ->add('year', EntityType::class,
                [
                    'label' => 'student.enrolment.label.year',
                    'class' => Year::class,
                    'choice_label' => 'name',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('y')
                            ->where('y.status != :yearStatus')
                            ->addOrderBy('y.firstDay', 'ASC')
                            ->setParameter('yearStatus', 'Archived');
                    },
                    'placeholder' => 'student.enrolment.placeholder.year',
                ]
            )
            ->add('students', HiddenType::class);

        $builder->get('students')->addModelTransformer(new EntityToStringTransformer($this->om, Student::class));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Busybee\StudentBundle\Entity\Enrolment',
            'translation_domain' => 'BusybeeStudentBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'enrolment';
    }


}
