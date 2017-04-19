<?php

namespace Busybee\TimeTableBundle\Form;

use Busybee\TimeTableBundle\Entity\LearningGroups;
use Busybee\TimeTableBundle\Events\LearningGroupSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LearningGroupsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('nameShort')
//            ->add('line')
            ->add('course');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => LearningGroups::class,
            'translation_domain' => 'BusybeeTimeTableBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tt_line_lgs';
    }


}
