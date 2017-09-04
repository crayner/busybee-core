<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Form;

use Busybee\People\PersonBundle\Entity\Student;
use Busybee\Core\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Busybee\Core\TemplateBundle\Type\YesNoType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Doctrine\Common\Persistence\ObjectManager;
use Busybee\Core\TemplateBundle\Form\DataTransformer\YesNoTransformer;

class ClientType extends AbstractType
{
	/**
	 * @var    Busybee\Core\SystemBundle\Setting\SettingManager
	 */
	private $sm;
	/**
	 * @var    Doctrine\Common\Persistence\ObjectManager
	 */
	private $manager;

	/**
	 * Construct
	 */
	public function __construct(SettingManager $sm, ObjectManager $manager)
	{
		$this->sm      = $sm;
		$this->manager = $manager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$x                              = array();
		$x[date('Y', strtotime('now'))] = date('Y', strtotime('now'));
		for ($i = 1; $i <= 100; $i++)
			$x[date('Y', strtotime('-' . $i . ' Years'))] = date('Y', strtotime('-' . $i . ' Years'));

		$builder
			->add('clientID', null,
				array(
					'label' => 'client.label.clientID',
					'attr'  => array(
						'help' => 'client.help.clientID',
					),
				)
			)
			->add('schoolAttainment', ChoiceType::class,
				array(
					'label'       => 'client.label.schoolAttainment',
					'attr'        => array(
						'help' => 'client.help.schoolAttainment',
					),
					'choices'     => $this->sm->get('AVETMISS.Client.schoolAttainment'),
					'placeholder' => 'client.placeholder.schoolAttainment',
				)
			)
			->add('schoolAttainmentYear', ChoiceType::class,
				array(
					'label'       => 'client.label.schoolAttainmentYear',
					'attr'        => array(
						'help' => 'client.help.schoolAttainmentYear',
					),
					'choices'     => $x,
					'placeholder' => 'client.placeholder.schoolAttainmentYear',
				)
			)
			->add('indigenous', ChoiceType::class,
				array(
					'label'       => 'client.label.indigenous',
					'attr'        => array(
						'help' => 'client.help.indigenous',
					),
					'choices'     => $this->sm->get('AVETMISS.Client.indigenous'),
					'placeholder' => 'client.placeholder.indigenous',
				)
			)
			->add('language', ChoiceType::class,
				array(
					'label'             => 'client.label.language',
					'attr'              => array(
						'help'  => 'client.help.language',
						'class' => 'languageSpoken',
					),
					'choices'           => $this->sm->get('AVETMISS.Client.language'),
					'placeholder'       => 'client.placeholder.language',
					'preferred_choices' => array('1201', '@@@@'),
				)
			)
			->add('labourForce', ChoiceType::class,
				array(
					'label'       => 'client.label.labourForce',
					'attr'        => array(
						'help' => 'client.help.labourForce',
					),
					'choices'     => $this->sm->get('AVETMISS.Client.labourForce'),
					'placeholder' => 'client.placeholder.labourForce',
				)
			)
			->add('countryBorn', ChoiceType::class,
				array(
					'label'             => 'client.label.countryBorn',
					'attr'              => array(
						'help' => 'client.help.countryBorn',
					),
					'choices'           => $this->sm->get('AVETMISS.country'),
					'placeholder'       => 'client.placeholder.countryBorn',
					'preferred_choices' => array('1101', '1201', '@@@@'),
				)
			)
			->add('student', HiddenType::class)
			->add('disability', ChoiceType::class,
				array(
					'label'       => 'client.label.disability',
					'attr'        => array(
						'help'  => 'client.help.disability',
						'class' => 'clientDisability',
					),
					'choices'     => $this->sm->get('AVETMISS.Client.Disability.identifiers'),
					'placeholder' => 'client.placeholder.disability',
				)
			)
			->add('priorEducation', ChoiceType::class,
				array(
					'label'       => 'client.label.priorEducation',
					'attr'        => array(
						'help' => 'client.help.priorEducation',
					),
					'choices'     => $this->sm->get('AVETMISS.Client.PriorEducation'),
					'placeholder' => 'client.placeholder.priorEducation',
				)
			)
			->add('atSchool', YesNoType::class,
				array(
					'label' => 'client.label.atSchool',
					'attr'  => array(
						'help' => 'client.help.atSchool',
					),
				)
			)
			->add('englishProficiency', ChoiceType::class,
				array(
					'label'       => 'client.label.englishProficiency',
					'attr'        => array(
						'help'  => 'client.help.englishProficiency',
						'class' => 'englishProficiency',
					),
					'choices'     => $this->sm->get('AVETMISS.Client.EnglishProficiency'),
					'placeholder' => 'client.placeholder.englishProficiency',
					'data'        => empty($options['data']->getEnglishProficiency()) ? '@' : $options['data']->getEnglishProficiency(),
					'required'    => false,
				)
			)
			->add('usi', null,
				array(
					'label'    => 'client.label.usi',
					'attr'     => array(
						'help' => 'client.help.usi',
					),
					'required' => false,
				)
			)
			->add('sal1', null,
				array(
					'label'    => 'client.label.sal1',
					'attr'     => array(
						'help'      => 'client.help.sal1',
						'maxLength' => 9,
					),
					'required' => false,
				)
			)
			->add('sal2', null,
				array(
					'label'    => 'client.label.sal2',
					'attr'     => array(
						'help'      => 'client.help.sal2',
						'maxLength' => 11,
					),
					'required' => false,
				)
			);
		$builder->get('student')
			->addModelTransformer(new EntityToStringTransformer($this->manager, Student::class));
		$builder->get('atSchool')
			->addModelTransformer(new YesNoTransformer());
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class'         => 'Busybee\AVETMISS\AVETMISSBundle\Entity\Client',
			'translation_domain' => 'BusybeeAVETMISSBundle',
			'mapped'             => false,
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'avetmiss_client';
	}


}
