<?php

namespace Busybee\FamilyBundle\Form;

use Busybee\PersonBundle\Entity\CareGiver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmergencyContactType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
			array(
				'translation_domain' => 'BusybeeFamilyBundle',
			)
		);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contact', EntityType::class,
                array(
                    'label'					=> 'family.label.emergencyContact',
                    'class'                 => CareGiver::class,
                    'choice_label'          => 'formatName',
                    'placeholder'           => 'family.placeholder.emergencyContact',
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'emergencyContact';
    }

}
