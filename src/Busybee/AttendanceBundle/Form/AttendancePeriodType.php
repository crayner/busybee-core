<?php

namespace Busybee\AttendanceBundle\Form;

use Busybee\AttendanceBundle\Entity\AttendancePeriod;
use Busybee\AttendanceBundle\Model\AttendanceManager;
use Busybee\CurriculumBundle\Entity\Course;
use Busybee\CurriculumBundle\Events\CourseSubscriber;
use Busybee\Core\TemplateBundle\Type\SettingChoiceType;
use Busybee\Core\TemplateBundle\Type\TextType;
use Busybee\TimeTableBundle\Entity\PeriodActivity;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttendancePeriodType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $manager = $options['manager'];
        $builder
            ->add('attendanceDate', ChoiceType::class,
                [
                    'label' => 'attendance.period.attendanceDate.label',
                    'attr' => [
                        'class' => 'monitorChange',
                        'help' => 'attendance.period.attendanceDate.help',
                    ],
                    'choices' => $options['manager']->getAttendDates(),
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => AttendancePeriod::class,
                'translation_domain' => 'BusybeeAttendanceBundle',
            ]
        );
        $resolver->setRequired(
            [
                'manager',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'attend_period';
    }


    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['manager'] = $options['manager'];
    }
}
