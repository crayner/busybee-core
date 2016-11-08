<?php

namespace Busybee\PersonBundle\Form ;

use Symfony\Component\Form\AbstractType ;
use Symfony\Component\Form\FormBuilderInterface ;
use Symfony\Component\OptionsResolver\OptionsResolver ;
use Busybee\PersonBundle\Form\Transformer\PhotoTransformer ;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine ;
use Symfony\Component\Validator\Constraints as Assert;

class PersonType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
					'label' => 'person.label.title',
					'choices' => $options['data']->getTitleList(),
					'attr'	=> array(
						'class' => 'beeTitle',
					),
					'required' => false,
					'constraints' => array(new Assert\Choice(array('groups' => 'person_form', 'choices' => $options['data']->getTitleList(true)))),
				)
			)
			->add('surname', null, array(
					'label' => 'person.label.surname',
					'attr'	=> array(
						'class' => 'beeSurname',
					),
					'constraints' => array(new Assert\NotBlank(array('groups' => 'person_form'))),
				)
			)
			->add('firstName', null, array(
					'label' => 'person.label.firstName',
					'attr'	=> array(
						'class' => 'beeFirstName',
					),
					'constraints' => array(new Assert\NotBlank(array('groups'=>'person_form'))),
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
					'constraints' => array(new Assert\NotBlank(array('groups'=>'person_form'))),
				)
			)
			->add('gender', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
					'choices' => $options['data']->getGenderList(),
					'label' => 'person.label.gender',
					'attr'	=> array(
						'class' => 'beeGender',
					),
					'constraints' => array(new Assert\Choice(array('groups'=>'person_form', 'choices' => $options['data']->getGenderList()))),
				)
			)
			->add('dob', 'Symfony\Component\Form\Extension\Core\Type\BirthdayType', array(
					'label' => 'person.dob.label',
					'required' => false,
					'attr'	=> array(
						'class' => 'beeDob',
					),
					'constraints' => array(new Assert\Date(array('groups'=>'person_form'))),
				)
			)
			->add('email', 'Symfony\Component\Form\Extension\Core\Type\EmailType', array(
					'label' => 'person.label.email',
					'required' => false,
					'constraints' => array(new Assert\Email(array('groups'=>'person_form', 'checkMX' => true))),
				)
			)
			->add('email2', 'Symfony\Component\Form\Extension\Core\Type\EmailType', array(
					'label' => 'person.label.email2',
					'required' => false,
					'constraints' => array(new Assert\Email(array('groups'=>'person_form', 'checkMX' => true))),
				)
			)
			->add('photo', 'Busybee\FormBundle\Type\ImageType', array(
					'attr' => array(
						'help' => 'person.help.photo' ,
						'imageClass' => 'headShot75',
					),
					'label' => 'person.label.photo',
					'required' => false,
					'constraints' => array(
						new Assert\Image(
							array(
								'groups' => 'person_form', 
								'maxSize' => '200k',
								'minWidth' => '350',
								'maxWidth' => '450',
								'minHeight' => '400',
								'maxHeight' => '500',
								'allowPortrait' => true,
								'allowLandscape' => false,
								'minRatio' => '0.75',
								'maxRatio' => '0.9',
							)
						)
					),
				)
			)
			->add('website', 'Symfony\Component\Form\Extension\Core\Type\UrlType', array(
					'label' => 'person.label.website',
					'required' => false,
					'constraints' => array(new Assert\Url(array('groups'=>'person_form'))),
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
					'attr'	=> array(
						'class' => 'beeAddressValueaddress1',
					),
					'data' => $options['data']->getAddress1(),
				)
			)
			->add('address2', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
					'attr'	=> array(
						'class' => 'beeAddressValueaddress2',
					),
					'data' => $options['data']->getAddress2(),
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
						'onClick'				=> "location.href='".$options['data']->cancelURL."'",
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
			'translation_domain' => 'BusybeePersonBundle',
			'validation_groups' => array('person_form'),
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
