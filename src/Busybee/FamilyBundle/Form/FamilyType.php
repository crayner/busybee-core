<?php

namespace Busybee\FamilyBundle\Form;

use Busybee\FamilyBundle\Events\FamilySubscriber;
use Busybee\FamilyBundle\Model\FamilyManager;
use Busybee\FormBundle\Type\AutoCompleteType;
use Busybee\PersonBundle\Entity\Address;
use Busybee\PersonBundle\Entity\CareGiver;
use Busybee\PersonBundle\Entity\Student;
use Busybee\PersonBundle\Form\PhoneType;
use Busybee\PersonBundle\Repository\CareGiverRepository;
use Busybee\PersonBundle\Repository\StudentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FamilyType extends AbstractType
{
    /**
     * @var FamilyManager
     */
    private $fm;

    public function __construct(FamilyManager $fm)
    {
        $this->fm = $fm;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                    'label' => 'family.label.name',
                    'required' => false,
                    'attr' => array(
                        'help' => 'family.help.name'
                    ),
                )
            )
            ->add('careGiver1', EntityType::class, array(
                    'label' => 'family.label.careGiver1',
                    'attr' => array(
                        'help' => 'family.help.careGiver1'
                    ),
                    'class' => CareGiver::class,
                    'choice_label' => 'formatName',
                    'placeholder' => 'family.placeholder.careGiver1',
                    'query_builder' => function (CareGiverRepository $cgr ) {
                        return $cgr->createQueryBuilder('c')
                            ->join('c.person', 'p')
                            ->where('c.person IS NOT NULL')
                            ->orderBy('p.surname')
                            ->addOrderBy('p.firstName')
                        ;
                    }
                )
            )
            ->add('careGiver2', EntityType::class, array(
                    'label' => 'family.label.careGiver2',
                    'attr' => array(
                        'help' => 'family.help.careGiver2'
                    ),
                    'class' => CareGiver::class,
                    'choice_label' => 'formatName',
                    'placeholder' => 'family.placeholder.careGiver2',
                    'required' => false,
                    'query_builder' => function (CareGiverRepository $cgr ) {
                        return $cgr->createQueryBuilder('c')
                            ->join('c.person', 'p')
                            ->where('c.person IS NOT NULL')
                            ->orderBy('p.surname')
                            ->addOrderBy('p.firstName')
                            ;
                    }
                )
            )
            ->add('address1', AutoCompleteType::class,
                array(
                    'class' => Address::class,
                    'data'  =>  $options['data']->getAddress1(),
                    'choice_label' => 'singleLineAddress',
                    'empty_data'  => null,
                    'required' => false,
                    'attr' => array(
                        'help' => 'person.help.address1',
                        'class' => 'beeAddressList formChanged',
                    ),
                    'translation_domain' => 'BusybeePersonBundle',
                )
            )
            ->add('address2', AutoCompleteType::class,
                array(
                    'class' => Address::class,
                    'choice_label' => 'singleLineAddress',
                    'data'  => $options['data']->getAddress2(),
                    'empty_data'  => null,
                    'required' => false,
                    'attr' => array(
                        'help' => 'person.help.address2',
                        'class' => 'beeAddressList formChanged',
                    ),
                    'translation_domain' => 'BusybeePersonBundle',
                )
            )
            ->add('phone', CollectionType::class, array(
                    'label'					=> 'person.label.phones',
                    'entry_type'			=> PhoneType::class,
                    'allow_add'				=> true,
                    'by_reference'			=> false,
                    'allow_delete'			=> true,
                    'attr'                  => array(
                        'class'                 => 'phoneNumberList'
                    ),
                    'translation_domain' => 'BusybeePersonBundle',
                )
            )
            ->add('emergencyContact', CollectionType::class, array(
                    'label'					=> 'family.label.emergencyContacts',
                    'entry_type'			=> EmergencyContactType::class,
                    'entry_options'         => array(
                        'translation_domain' => 'BusybeeFamilyBundle',
                        'class' => CareGiver::class,
                        'choice_label' => 'formatName',
                        'query_builder' => function (CareGiverRepository $cgr ) {
                            return $cgr->createQueryBuilder('c')
                                ->join('c.person', 'p')
                                ->where('c.person IS NOT NULL')
                                ->orderBy('p.surname')
                                ->addOrderBy('p.firstName');
                        },
                        'placeholder' => 'family.placeholder.emergencyContact',
                    ),
                    'allow_add'				=> true,
                    'by_reference'			=> false,
                    'allow_delete'			=> true,
                    'attr'                  => array(
                        'class'                 => 'emergencyList',
                        'help'                  => 'family.help.emergencyContacts',
                    ),
                    'required'              => false,
                )
            )
            ->add('students', CollectionType::class, array(
                    'label'					=> 'family.label.students',
                    'entry_type'			=> StudentType::class,
                    'entry_options'         => array(
                        'translation_domain' => 'BusybeeFamilyBundle',
                        'class' => Student::class,
                        'choice_label' => 'formatName',
                        'query_builder' => function (StudentRepository $sr ) {
                            return $sr->createQueryBuilder('s')
                                ->join('s.person', 'p')
                                ->where('s.person IS NOT NULL')
                                ->orderBy('p.surname')
                                ->addOrderBy('p.firstName');
                        },
                        'placeholder' => 'family.placeholder.student',
                    ),
                    'allow_add'				=> true,
                    'by_reference'			=> false,
                    'allow_delete'			=> true,
                    'attr'                  => array(
                        'class'                 => 'studentList',
                        'help'                  => 'family.help.students',
                    ),
                    'required'              => false,
                )
            )
        ;
        $builder->addEventSubscriber(new FamilySubscriber($this->fm));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Busybee\FamilyBundle\Entity\Family',
            'translation_domain' => 'BusybeeFamilyBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'family';
    }


}
