<?php

namespace Busybee\PersonBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title')
			->add('surname', null, array(
					'label' => 'person.label.surname',
				)
			)
			->add('firstName', null, array(
					'label' => 'person.label.firstName',
				)
			)
			->add('preferredName', null, array(
					'label' => 'person.label.preferredName',
				)
			)
			->add('officialName', null, array(
					'label' => 'person.label.officialName',
					'attr' => array(
						'help' => 'person.help.officialName',
					),
				)
			)
			->add('gender', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
					'choices' => $options['data']->getGenders(),
					'label' => 'person.label.gender',
				)
			)
			->add('dob', null, array(
					'years' => $this->getYears(),
					'label' => 'person.dob.label',
					'required' => false,
				)
			)
			->add('email', 'Symfony\Component\Form\Extension\Core\Type\EmailType', array(
					'label' => 'person.label.email',
					'required' => false,
				)
			)
			->add('email2', 'Symfony\Component\Form\Extension\Core\Type\EmailType', array(
					'label' => 'person.label.email2',
					'required' => false,
				)
			)
			->add('photo', 'Busybee\FormBundle\Type\ImageType', array(
					'attr' => array(
						'help' => 'person.help.photo' ,
					),
					'label' => 'person.label.photo',
					'required' => false,
				)
			)
			->add('website', 'Symfony\Component\Form\Extension\Core\Type\UrlType', array(
					'label' => 'person.label.website',
				)
			)
			->add('address1', 'Busybee\PersonBundle\Form\AddressType', array(
					'data' => $options['data']->getAddress1(),
					'required' => false,
				)
			)
			->add('address2', 'Busybee\PersonBundle\Form\AddressType', array(
					'data' => $options['data']->getAddress2(),
					'required' => false,
				)
			)
            ->add('save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label' 				=> 'form.save',
					'translation_domain' 	=> 'BusybeeHomeBundle',
					'attr' 					=> array(
						'class' 				=> 'btn btn-success glyphicons glyphicons-disk-save'
					),
				)
			)
			->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
					'label'					=> 'form.cancel', 
					'translation_domain' 	=> 'BusybeeHomeBundle',
					'attr' 					=> array(
						'formnovalidate' 		=> 'formnovalidate',
						'class' 				=> 'btn btn-info glyphicons glyphicons-remove-circle',
						'onClick'				=> 'location.href=\''.$options['data']->cancelURL."'",
					),
				)
			)
		;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Busybee\PersonBundle\Entity\Person',
			'translation_domain' 	=> 'BusybeePersonBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'person';
    }
    /**
     * {@inheritdoc}
     */
    public function getYears()
    {
        $years = array();
		for($i=-100; $i<=0; $i++)
		{
			$years[] = date('Y', strtotime($i.' Years'));
		}
		return $years ;
    }
}
