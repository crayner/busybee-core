<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Busybee\Core\TemplateBundle\Type\YesNoType;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Busybee\AVETMISS\AVETMISSBundle\Form\DataTransformer\SubjectTransformer;
use Doctrine\Common\Persistence\ObjectManager;

class SubjectType extends AbstractType
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
		$builder
			->add('identifier', null,
				array(
					'label' => 'subject.label.identifier',
					'attr'  => array(
						'help'  => 'subject.help.identifier',
						'class' => 'subjectForm',
					)
				)
			)
			->add('nominalHours', null,
				array(
					'label'    => 'subject.label.nominalHours',
					'attr'     => array(
						'help'  => 'subject.help.nominalHours',
						'class' => 'subjectForm',
					),
					'required' => false,
				)
			)
			->add('FOEIdentifier', ChoiceType::class,
				array(
					'label'       => 'subject.label.FOEIdentifier',
					'attr'        => array(
						'help'  => 'subject.help.FOEIdentifier',
						'class' => 'subjectForm',
					),
					'choices'     => $this->sm->get('AVETMISS.Subject.FOE'),
					'placeholder' => 'subject.placeholder.FOEIdentifier',
					'required'    => false,
				)
			)
			->add('VETFlag', YesNoType::class,
				array(
					'label' => 'subject.label.VETFlag',
					'attr'  => array(
						'help'  => 'subject.help.VETFlag',
						'class' => 'subjectForm',
					),
				)
			)
			->add('subjectFlag', YesNoType::class,
				array(
					'label' => 'subject.label.subjectFlag',
					'attr'  => array(
						'help'              => 'subject.help.subjectFlag',
						'data-off-label'    => "false",
						'data-on-label'     => "false",
						'data-off-icon-cls' => "glyphicons-thumbs-down",
						'data-on-icon-cls'  => "glyphicons-thumbs-up",
						'class'             => 'subjectForm',
					),
				)
			)
			->add('save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label'              => 'form.save',
					'translation_domain' => 'BusybeeHomeBundle',
					'attr'               => array(
						'class' => 'btn btn-success glyphicons glyphicons-disk-save',
					),
				)
			)
			->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
					'label'              => 'form.cancel',
					'translation_domain' => 'BusybeeHomeBundle',
					'attr'               => array(
						'formnovalidate' => 'formnovalidate',
						'class'          => 'btn btn-info glyphicons glyphicons-remove-circle',
						'onClick'        => "location.href='" . $options['data']->cancelURL . "'",
					),
				)
			)
			->add('subjectList', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
					'class'        => 'BusybeeAVETMISSBundle:Subject',
					'label'        => 'subject.label.subject',
					'attr'         => array(
						'class' => 'subjectList',
						'help'  => 'subject.help.subject'
					),
					'mapped'       => false,
					'choice_label' => 'nameVersion',
					'placeholder'  => 'subject.placeholder.subject',
					'required'     => false,
					'data'         => $options['data']->core,
				)
			)
			->add('name', null,
				array(
					'mapped'   => false,
					'label'    => 'subject.label.name',
					'attr'     => array(
						'help'  => 'subject.help.name',
						'class' => 'subjectForm',
					),
					'required' => true,
					'data'     => $options['data']->name,
				)
			)
			->add('version', null,
				array(
					'mapped'   => false,
					'label'    => 'subject.label.version',
					'attr'     => array(
						'help'  => 'subject.help.version',
						'class' => 'subjectForm',
					),
					'required' => false,
					'data'     => $options['data']->version,
				)
			)
			->add('subject', 'Symfony\Component\Form\Extension\Core\Type\HiddenType');
		$builder->get('subject')->addModelTransformer(new SubjectTransformer($this->manager));
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class'         => 'Busybee\AVETMISS\AVETMISSBundle\Entity\Subject',
			'translation_domain' => 'BusybeeAVETMISSBundle',
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'avetmiss_subject';
	}
}
