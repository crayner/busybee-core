<?php

namespace Busybee\PersonBundle\Form;

use Busybee\PersonBundle\Entity\Person;
use Busybee\PersonBundle\Entity\Student;
use Busybee\PersonBundle\Repository\PersonRepository;
use Busybee\PersonBundle\Repository\StudentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('person', EntityType::class, array(
                'label' => 'student.label.person',
                'attr' => array(
                    'help'  =>  'student.help.person',
                ),
                'class' => Person::class,
                'placeholder' => 'student.placeholder.person',
                'choice_label' => 'formatName',
/*                'query_builder' => function (PersonRepository $pr) {
                    return $pr->createQueryBuilder('p')
                        ->orderBy('p.surname', 'ASC')
                        ->addOrderBy('p.firstName', 'ASC')
                        ;
                },
*/
            )
        );

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Busybee\PersonBundle\Entity\Student',
            'translation_domain' => 'BusybeePersonBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'student';
    }


}
