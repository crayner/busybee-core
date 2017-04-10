<?php

namespace Busybee\PersonBundle\Form;

use Busybee\PersonBundle\Entity\Person;
use Busybee\PersonBundle\Entity\PersonExtra;
use Busybee\PersonBundle\Entity\Phone;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExtraType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * Construct
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('person', HiddenType::class,
                [
                    'label' => 'person.extra.label.person',
                ]
            )
            ->add('vehicleRegistration', null,
                [
                    'label' => 'person.extra.label.vehicleRegistration',
                ]
            );

        $builder->get('person')->addModelTransformer(new EntityToStringTransformer($this->om, Person::class));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => PersonExtra::class,
                'translation_domain' => 'BusybeePersonBundle',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'person_extra';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'person_extra';
    }


}
