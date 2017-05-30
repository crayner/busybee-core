<?php

namespace Busybee\TimeTableBundle\Form;

use Busybee\TimeTableBundle\Entity\Period;
use Busybee\TimeTableBundle\Entity\PeriodActivity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodActivityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
                        ],
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
            ]
        );
        $resolver->setRequired([
            'year_data',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tt_period_activity';
    }


}
