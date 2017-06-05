<?php

namespace Busybee\TimeTableBundle\Form;

use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\StudentBundle\Entity\Activity;
use Busybee\TimeTableBundle\Entity\Period;
use Busybee\TimeTableBundle\Entity\PeriodActivity;
use Busybee\TimeTableBundle\Events\PeriodActivitySubscriber;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodActivityEntityType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * PeriodActivityEntityType constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $year = $options['year_data'];
        $builder
            ->add('activity', EntityType::class,
                [
                    'class' => Activity::class,
                    'choice_label' => 'fullName',
                    'placeholder' => 'period.activity.placeholder',
                    'query_builder' => function (EntityRepository $er) use ($year) {
                        return $er->createQueryBuilder('a')
                            ->orderBy('a.name', 'ASC')
                            ->where('a.year = :year')
                            ->setParameter('year', $year);
                    },
                    'attr' =>
                        [
                            'class' => 'input-sm',
                        ],

                ]
            )
            ->add('period', HiddenType::class);
        $builder->get('period')->addModelTransformer(new EntityToStringTransformer($this->om, Period::class));
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => PeriodActivity::class,
                'translation_domain' => 'BusybeeTimeTableBundle',
                'class' => PeriodActivity::class,
                'choice_label' => 'fullName',
                'error_bubbling' => true,
            ]
        );
        $resolver->setRequired(
            [
                'year_data',
                'manager',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'period_activity';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['manager'] = $options['manager'];
    }
}
