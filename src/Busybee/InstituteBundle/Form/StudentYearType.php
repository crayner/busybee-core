<?php

namespace Busybee\InstituteBundle\Form;

use Busybee\InstituteBundle\Entity\StudentYear;
use Busybee\InstituteBundle\Entity\Year;
use Busybee\InstituteBundle\Form\DataTransformer\YearTransformer;
use Busybee\InstituteBundle\Model\YearManager;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class StudentYearType extends AbstractType
{
    /**
     * @var YearManager
     */
    private $manager;

    /**
     * Construct
     */
    public function __construct(YearManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $year = empty($options['year_data']->getName()) ? '' : $options['year_data']->getName();
        $builder
            ->add('name', null,
                array(
                    'label' => 'groups.year.label.name',
                    'attr' => array(
                        'help' => array('groups.year.help.unique', array('%year%' => $year)),
                    ),
                )
            )
            ->add('nameShort', null,
                array(
                    'label' => 'groups.year.label.nameShort',
                    'attr' => array(
                        'help' => array('groups.year.help.unique', array('%year%' => $year)),
                    ),
                )
            )
            ->add('year', HiddenType::class)
            ->add('sequence', HiddenType::class);
        $builder->get('year')
            ->addModelTransformer(new EntityToStringTransformer($this->manager->getObjectManager(), Year::class));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => StudentYear::class,
                'translation_domain' => 'BusybeeInstituteBundle',
                'year_data' => null,
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'studentyear';
    }


}
