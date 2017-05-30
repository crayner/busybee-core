<?php

namespace Busybee\TimeTableBundle\Form;

use Busybee\StudentBundle\Entity\Activity;
use Busybee\TimeTableBundle\Entity\PeriodActivity;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodActivityEntityType extends AbstractType
{
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
                'data_class' => PeriodActivity::class,
                'translation_domain' => 'BusybeeTimeTableBundle',
                'class' => PeriodActivity::class,
                'choice_label' => 'fullName',
            ]
        );
        $resolver->setRequired(
            [
                'year_data',
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

}
