<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Symfony\Component\Validator\Constraints\NotBlank;

class CourseType extends AbstractType
{
	/**
	 * @var    Busybee\Core\SystemBundle\Setting\SettingManager
	 */
	private $sm;

	/**
	 * Construct
	 */
	public function __construct(SettingManager $sm)
	{
		$this->sm = $sm;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('identifier', null,
				array(
					'label' => 'course.label.identifier',
					'attr'  => array(
						'help'  => 'course.help.identifier',
						'class' => 'courseForm',
					)
				)
			)
			->add('name', null,
				array(
					'mapped'      => false,
					'label'       => 'course.label.name',
					'attr'        => array(
						'help'  => 'course.help.name',
						'class' => 'courseForm',
					),
					'constraints' => array(
						new NotBlank(),
					),
					'data'        => $options['data']->name,
					'required'    => true,
				)
			)
			->add('version', null,
				array(
					'mapped'   => false,
					'label'    => 'course.label.version',
					'attr'     => array(
						'help'  => 'course.help.version',
						'class' => 'courseForm',
					),
					'data'     => $options['data']->version,
					'required' => false,
				)
			)
			->add('nominalHours', null,
				array(
					'label' => 'course.label.nominalHours',
					'attr'  => array(
						'help'  => 'course.help.nominalHours',
						'class' => 'courseForm',
					),
				)
			)
			->add('recognitionIdentifier', ChoiceType::class,
				array(
					'label'       => 'course.label.recognitionIdentifier',
					'attr'        => array(
						'help'  => 'course.help.recognitionIdentifier',
						'class' => 'courseForm',
					),
					'choices'     => $this->sm->get('AVETMISS.Recognition.Identifier.choices'),
					'placeholder' => 'course.placeholder.recognitionIdentifier',
				)
			)
			->add('levelEducationIdentifier', ChoiceType::class,
				array(
					'label'       => 'course.label.levelEducationIdentifier',
					'attr'        => array(
						'help'  => 'course.help.levelEducationIdentifier',
						'class' => 'courseForm',
					),
					'choices'     => $this->sm->get('AVETMISS.Level.Education'),
					'placeholder' => 'course.placeholder.levelEducationIdentifier',
				)
			)
			->add('FOEIdentifier', ChoiceType::class,
				array(
					'label'       => 'course.label.FOEIdentifier',
					'attr'        => array(
						'help'  => 'course.help.FOEIdentifier',
						'class' => 'courseForm',
					),
					'choices'     => $this->sm->get('AVETMISS.Program.FOE'),
					'placeholder' => 'course.placeholder.FOEIdentifier',
				)
			)
			->add('ANZSCOIdentifier', null,
				array(
					'label' => 'course.label.ANZSCOIdentifier',
					'attr'  => array(
						'help'  => 'course.help.ANZSCOIdentifier',
						'class' => 'courseForm',
					),
				)
			)
			->add('VETFlag', 'Busybee\Core\TemplateBundle\Type\YesNoType',
				array(
					'label' => 'course.label.VETFlag',
					'attr'  => array(
						'help'  => 'course.help.VETFlag',
						'class' => 'courseForm',
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
			->add('course', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
					'class'        => 'BusybeeAVETMISSBundle:Course',
					'label'        => 'course.label.course',
					'attr'         => array(
						'class' => 'courseList',
						'help'  => 'course.help.course'
					),
					'mapped'       => false,
					'choice_label' => 'nameVersion',
					'placeholder'  => 'course.placeholder.course',
					'required'     => false,
					'data'         => $options['data']->core,
				)
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class'         => 'Busybee\AVETMISS\AVETMISSBundle\Entity\Course',
			'translation_domain' => 'BusybeeAVETMISSBundle',
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'avetmiss_course';
	}


}
