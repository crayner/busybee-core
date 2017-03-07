<?php

namespace Busybee\InstituteBundle\Form ;

use Busybee\InstituteBundle\Entity\Term;
use Symfony\Component\Form\AbstractType ;
use Symfony\Component\Form\FormBuilderInterface ;
use Symfony\Component\OptionsResolver\OptionsResolver ;
use Symfony\Component\Form\Extension\Core\Type\HiddenType ;
use Busybee\InstituteBundle\Form\DataTransformer\YearTransformer ;
use Doctrine\ORM\EntityManager as ObjectManager;

class TermType extends AbstractType
{
    /**
     * @var    ObjectManager
     */
    private $manager;

    /**
     * Construct
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
        $terms = $options['year_data']->getTerms();
        $year = $options['year_data'];
        $years = array();
        if (!is_null($year->getFirstDay())) {
            $years[] = $year->getFirstDay()->format('Y');
            if ($year->getFirstDay()->format('Y') !== $year->getLastDay()->format('Y'))
                $years[] = $year->getLastDay()->format('Y');
        } else
            $years[] = date('Y');
        $builder
            ->add('name', null,
                array(
                    'label' => 'term.label.name',
                    'attr' => array(
                        'help' => 'term.help.name',
                    ),
                )
            )
            ->add('nameShort', null,
                array(
                    'label' => 'term.label.nameShort',
                    'attr' => array(
                        'help' => 'term.help.nameShort',
                    ),
                )
            )
            ->add('firstDay', null,
                array(
                    'label' => 'calendar.label.firstDay',
                    'attr' => array(
                        'help' => 'calendar.help.firstDay',
                    ),
                    'years' => $years,
                )
            )
            ->add('lastDay', null,
                array(
                    'label' => 'calendar.label.lastDay',
                    'attr' => array(
                        'help' => 'calendar.help.lastDay',
                    ),
                    'years' => $years,
                )
            )
            ->add('year', HiddenType::class);
        $builder->get('year')
            ->addModelTransformer(new YearTransformer($this->manager));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Term::class,
                'translation_domain' => 'BusybeeInstituteBundle',
                'year_data' => null,
                'error_bubbling' => true,
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'term';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'term';
    }


}
