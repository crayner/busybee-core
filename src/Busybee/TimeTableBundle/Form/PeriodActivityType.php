<?php
namespace Busybee\TimeTableBundle\Form;

use Busybee\TimeTableBundle\Entity\Line;
use Busybee\TimeTableBundle\Entity\Period;
use Busybee\TimeTableBundle\Entity\PeriodActivity;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodActivityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $year = $options['year_data'];
        $tt = $options['manager']->getTimeTable();
        $builder
            ->add('activities', CollectionType::class,
                [
                    'label' => false,
                    'entry_type' => PeriodActivityEntityType::class,
                    'required' => true,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'attr' => [
                        'class' => 'periodActivityList',
                    ],
                    'entry_options' =>
                        [
                            'year_data' => $options['year_data'],
                            'manager' => $options['manager'],
                        ],
                ]
            )
            ->add('line', EntityType::class,
                [
                    'mapped' => false,
                    'class' => Line::class,
                    'choice_label' => 'fullName',
                    'query_builder' => function (EntityRepository $er) use ($year) {
                        return $er->createQueryBuilder('l')
                            ->where('l.year = :year_id')
                            ->setParameter('year_id', $year->getId())
                            ->orderBy('l.name', 'ASC');
                    },
                    'attr' => [
                        'class' => 'lineList changeRecord',
                    ],
                    'placeholder' => 'period.activities.activity.addline.placeholder',
                    'required' => false,
                ]
            )
            ->add('periods', EntityType::class,
                [
                    'mapped' => false,
                    'class' => Period::class,
                    'choice_label' => 'columnName',
                    'query_builder' => function (EntityRepository $er) use ($tt) {
                        return $er->createQueryBuilder('p')
                            ->leftJoin('p.column', 'c')
                            ->leftJoin('c.timetable', 't')
                            ->where('t.id = :tt_id')
                            ->orderBy('c.sequence', 'ASC')
                            ->addOrderBy('p.start', 'ASC')
                            ->setParameter('tt_id', $tt->getId());
                    },
                    'attr' => [
                        'class' => 'periodList changeRecord',
                    ],
                    'placeholder' => 'period.activities.duplicate.placeholder',
                    'required' => false,
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
                'data_class' => Period::class,
                'translation_domain' => 'BusybeeTimeTableBundle',
                'error_bubbling' => true,
            ]
        );
        $resolver->setRequired([
            'year_data',
            'manager',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tt_period_activity';
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['manager'] = $options['manager'];
    }
}
