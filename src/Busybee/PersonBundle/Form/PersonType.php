<?php

namespace Busybee\PersonBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Busybee\PersonBundle\Form\Transformer\PhotoTransformer ;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class PersonType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
					'label' => 'person.label.title',
					'choices' => $options['data']->titleList,
					'attr'	=> array(
						'class' => 'beeTitle',
					),
					'required' => false,
				)
			)
			->add('surname', null, array(
					'label' => 'person.label.surname',
					'attr'	=> array(
						'class' => 'beeSurname',
					),
				)
			)
			->add('firstName', null, array(
					'label' => 'person.label.firstName',
					'attr'	=> array(
						'class' => 'beeFirstName',
					),
				)
			)
			->add('preferredName', null, array(
					'label' => 'person.label.preferredName',
					'attr'	=> array(
						'class' => 'beePreferredName',
					),
					'required' => false,
				)
			)
			->add('officialName', null, array(
					'label' => 'person.label.officialName',
					'attr' => array(
						'help' => 'person.help.officialName',
						'class' => 'beeOfficialName',
					),
				)
			)
			->add('gender', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
					'choices' => $options['data']->genderList,
					'label' => 'person.label.gender',
					'attr'	=> array(
						'class' => 'beeGender',
					),
				)
			)
			->add('dob', 'Symfony\Component\Form\Extension\Core\Type\BirthdayType', array(
					'label' => 'person.dob.label',
					'required' => false,
					'attr'	=> array(
						'class' => 'beeDob',
					),
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
						'imageClass' => 'headShot75',
					),
					'label' => 'person.label.photo',
					'required' => false,
					'data' => $options['data']->getPhoto(),
					'data_class' => null,
				)
			)
			->add('website', 'Symfony\Component\Form\Extension\Core\Type\UrlType', array(
					'label' => 'person.label.website',
					'required' => false,
				)
			)
			->add('add1', 'Busybee\PersonBundle\Form\AddressType', array(
					'data' => $options['data']->getAddress1Record(),
					'required' => false,
					'mapped' => false,
				)
			)
			->add('add2', 'Busybee\PersonBundle\Form\AddressType', array(
					'data' => $options['data']->getAddress2Record(),
					'required' => false,
					'mapped' => false,
				)
			)
			->add('address1', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
				)
			)
			->add('address2', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
				)
			)
			->add('save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label' 				=> 'form.save',
					'translation_domain' 	=> 'BusybeeHomeBundle',
					'attr' 					=> array(
						'class' 				=> 'btn btn-success glyphicons glyphicons-disk-save',
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
		$builder->get('photo')->addModelTransformer($options['data']->transformer);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Busybee\PersonBundle\Entity\Person',
			'translation_domain' => 'BusybeePersonBundle',
			'validation_groups' => 'default',
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
