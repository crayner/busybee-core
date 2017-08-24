<?php

namespace Busybee\People\StudentBundle\Form;

use Busybee\Core\FormBundle\Type\ImageType;
use Busybee\Core\FormBundle\Type\SettingChoiceType;
use Busybee\People\PersonBundle\Entity\Person;
use Busybee\Core\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Busybee\People\StudentBundle\Entity\Student;
use Busybee\People\StudentBundle\Events\StudentSubscriber;
use Busybee\People\StudentBundle\Validator\Constraints\GradesValidator;
use Busybee\People\StudentBundle\Validator\Grades;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
	/**
	 * @var ObjectManager
	 */
	private $om;

	/**
	 * @var SettingManager
	 */
	private $sm;

	/**
	 * StaffType constructor.
	 *
	 * @param ObjectManager $om
	 */
	public function __construct(ObjectManager $om, SettingManager $sm)
	{
		$this->om = $om;
		$this->sm = $sm;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('person', HiddenType::class,
				array(
					'attr' => array(
						'class' => 'student',
					)
				)
			)
			->add('startAtSchool', DateType::class, array(
					'years' => range(date('Y', strtotime('-25 years')), date('Y', strtotime('+1 year'))),
					'label' => 'student.label.startAtSchool',
					'attr'  => array(
						'help'  => 'student.help.startAtSchool',
						'class' => 'student',
					),
				)
			)
			->add('startAtThisSchool', DateType::class, array(
					'years' => range(date('Y', strtotime('-25 years')), date('Y', strtotime('+1 year'))),
					'label' => 'student.label.startAtThisSchool',
					'attr'  => array(
						'help'  => 'student.help.startAtThisSchool',
						'class' => 'student',
					),
				)
			)
			->add('lastAtThisSchool', DateType::class, array(
					'years'    => range(date('Y', strtotime('-5 years')), date('Y', strtotime('+18 months'))),
					'label'    => 'student.label.lastAtThisSchool',
					'attr'     => array(
						'help'  => 'student.help.lastAtThisSchool',
						'class' => 'student',
					),
					'required' => false,
				)
			)
			->add('firstLanguage', LanguageType::class, array(
					'label'       => 'student.label.language.first',
					'placeholder' => 'student.placeholder.language',
					'required'    => false,
				)
			)
			->add('secondLanguage', LanguageType::class, array(
					'label'       => 'student.label.language.second',
					'placeholder' => 'student.placeholder.language',
					'required'    => false,
				)
			)
			->add('thirdLanguage', LanguageType::class, array(
					'label'       => 'student.label.language.third',
					'placeholder' => 'student.placeholder.language',
					'required'    => false,
				)
			)
			->add('countryOfBirth', CountryType::class, array(
					'label'       => 'student.label.countryOfBirth',
					'placeholder' => 'student.placeholder.countryOfBirth',
					'required'    => false,
				)
			)
			->add('ethnicity', SettingChoiceType::class,
				array(
					'label'        => 'student.label.ethnicity',
					'placeholder'  => 'student.placeholder.ethnicity',
					'required'     => false,
					'setting_name' => 'Ethnicity.List',
				)
			)
			->add('religion', SettingChoiceType::class,
				array(
					'label'        => 'student.label.religion',
					'placeholder'  => 'student.placeholder.religion',
					'required'     => false,
					'setting_name' => 'Religion.List',
				)
			)
			->add('citizenship1', CountryType::class,
				array(
					'label'       => 'student.label.citizenship.1',
					'placeholder' => 'student.placeholder.citizenship',
					'required'    => false,
				)
			)
			->add('citizenship2', CountryType::class,
				array(
					'label'       => 'student.label.citizenship.2',
					'placeholder' => 'student.placeholder.citizenship',
					'required'    => false,
				)
			)
			->add('citizenship1Passport', null,
				array(
					'label'    => 'student.label.citizenship.passport',
					'required' => false,
				)
			)
			->add('citizenship2Passport', null,
				array(
					'label'    => 'student.label.citizenship.passport',
					'required' => false,
				)
			)
			->add('locker', null,
				array(
					'label'    => 'student.label.locker',
					'required' => false,
				)
			)
			->add('citizenship1PassportScan', ImageType::class, array(
					'attr'     => array(
						'help'       => 'student.help.passportScan',
						'imageClass' => 'headShot75',
					),
					'label'    => 'student.label.passportScan',
					'required' => false,
				)
			)
			->add('nationalIDCardNumber', null, array(
					'label'    => 'student.label.nationalIDCardNumber',
					'required' => false,
				)
			)
			->add('nationalIDCardScan', ImageType::class, array(
					'attr'     => array(
						'help'       => 'student.help.nationalIDCardScan',
						'imageClass' => 'headShot75',
					),
					'label'    => 'student.label.nationalIDCardScan',
					'required' => false,
				)
			)
			->add('residencyStatus', SettingChoiceType::class,
				array(
					'label'        => 'student.label.residencyStatus',
					'placeholder'  => 'student.placeholder.residencyStatus',
					'required'     => false,
					'setting_name' => 'Residency.List',
					'attr'         => array(
						'help' => 'student.help.residencyStatus',
					),
				)
			)
			->add('visaExpiryDate', DateType::class, array(
					'years'    => range(date('Y', strtotime('-1 years')), date('Y', strtotime('+10 year'))),
					'label'    => 'student.label.visaExpiryDate',
					'attr'     => array(
						'help'  => 'student.help.visaExpiryDate',
						'class' => 'student',
					),
					'required' => false,
				)
			)
			->add('house', SettingChoiceType::class, array(
					'label'                     => 'family.label.house',
					'placeholder'               => 'family.placeholder.house',
					'required'                  => false,
					'attr'                      => array(
						'help' => 'family.help.house',
					),
					'setting_name'              => 'house.list',
					'translation_domain'        => 'BusybeeFamilyBundle',
					'choice_translation_domain' => 'BusybeeFamilyBundle',
				)
			)
			->add('grades', CollectionType::class,
				[
					'label'         => 'student.label.grades',
					'allow_add'     => true,
					'allow_delete'  => true,
					'entry_type'    => StudentGradeType::class,
					'attr'          => [
						'class' => 'gradeList',
						'help'  => 'student.help.grades',
					],
					'entry_options' => [
						'year_data' => $options['year_data'],
					],
					'constraints'   => [
						new Grades(),
					],

				]
			);

		$builder->get('person')->addModelTransformer(new EntityToStringTransformer($this->om, Person::class));

		$builder->addEventSubscriber(new StudentSubscriber($this->om));
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver
			->setDefaults(
				[
					'data_class'         => Student::class,
					'translation_domain' => 'BusybeeStudentBundle',
					'error_bubbling'     => true,
					'year_data'          => null,
				]
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'student';
	}


}
