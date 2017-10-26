<?php
namespace Busybee\People\PersonBundle\Events;

use Busybee\Core\TemplateBundle\Model\TabManager;
use Busybee\Core\TemplateBundle\Type\ImageType;
use Busybee\Core\TemplateBundle\Type\SettingChoiceType;
use Busybee\Core\TemplateBundle\Type\TextType;
use Busybee\Management\GradeBundle\Entity\StudentGrade;
use Busybee\Management\GradeBundle\Form\StudentGradeType;
use Busybee\People\PersonBundle\Form\UserType;
use Busybee\People\PersonBundle\Model\PersonManager;
use Busybee\Core\SecurityBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\File;

class PersonSubscriber implements EventSubscriberInterface
{
	/**
	 * @var PersonManager
	 */
	private $personManager;

	/**
	 * @var ObjectManager
	 */
	private $om;

	/**
	 * @var TabManager
	 */
	private $tm;

	/**
	 * @var
	 */
	private $isSystemAdmin;

	/**
	 * PersonSubscriber constructor.
	 *
	 * @param PersonManager $pm
	 * @param ObjectManager $om
	 * @param               $parameters
	 */
	public function __construct(PersonManager $pm, ObjectManager $om, TabManager $tm, bool $isSystemAdmin)
	{
		$this->personManager = $pm;
		$this->om            = $om;
		$this->tm            = $tm;
		$this->isSystemAdmin = $isSystemAdmin;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		// Tells the dispatcher that you want to listen on the form.pre_submit
		// event and that the preSubmit method should be called.
		return array(
			FormEvents::PRE_SET_DATA => 'preSetData',
			FormEvents::PRE_SUBMIT   => 'preSubmit',
		);
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSetData(FormEvent $event)
	{
		$person = $event->getData();
		$form   = $event->getForm();

		if ($person->isStaff())
			$this->addStaffFields($form);

		if ($person->isStudent())
			$this->addStudentFields($form);

		if ($person->isUser())
		{
			$user = is_null($person->getUser()) ? new User() : $person->getUser();
			$person->setUser($user);
			$form->add('user', UserType::class, ['isSystemAdmin' => $this->isSystemAdmin, 'data' => $user]);
			if (empty($person->getUser()->getEmail()) || $person->getUser()->getEmail() != $person->getEmail())
				$person->getUser()->setEmail($person->getEmail());
		}

		$event->setData($person);
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{
		$data   = $event->getData();
		$form   = $event->getForm();
		$person = $form->getData();
		$flush  = false;

		if ($form->has('user'))
		{
			if (isset($data['userQuestion']) && $data['userQuestion'] === '1' && !$person->getUser() instanceof User && $this->personManager->canBeUser($person))
			{
				$user                                = $this->personManager->doesThisUserExist($person);
				$data['user']                        = array();
				$data['user']['person']              = $form->getData()->getId();
				$data['user']['email']               = $data['user']['emailCanonical'] = $data['email'];
				$data['user']['username']            = $data['user']['usernameCanonical'] = $data['email'];
				$data['user']['locale']              = $this->personManager->getParameter('locale');
				$data['user']['enabled']             = true;
				$data['user']['locked']              = false;
				$data['user']['expired']             = false;
				$data['user']['credentials_expired'] = true;
				$data['user']['password']            = password_hash(uniqid(), PASSWORD_BCRYPT);
				if ($user instanceof User)
				{
					$user->getRoles();
					$data['user']                        = array();
					$data['user']['person']              = $form->getData()->getId();
					$data['user']['email']               = $data['user']['emailCanonical'] = $user->getEmail();
					$data['user']['username']            = $data['user']['usernameCanonical'] = $user->getEmail();
					$data['user']['locale']              = $user->getLocale();
					$data['user']['enabled']             = $user->getEnabled();
					$data['user']['locked']              = $user->getLocked();
					$data['user']['expired']             = $user->getExpired();
					$data['user']['credentials_expired'] = $user->getCredentialsExpired();
					$data['user']['password']            = $user->getPassword();
					$data['user']['directroles']         = array();
					if (is_array($roles = $user->getRoles()))
						foreach ($roles as $role)
						{
							$data['user']['directroles'][] = $role;
						}
					$data['user']['groups'] = array();
					if (is_array($groups = $user->getGroups()))
						foreach ($groups as $group)
						{
							$data['user']['groups'][] = $group;
						}
					$person->setUser($user);
				}

				$form->remove('user');
				$form->add('user', UserType::class);

			}

			if ($form->get('user')->getData() instanceof User && isset($data['user']))
			{
				$user = $person->getUser();
				if ($user instanceof User)
				{
					$data['user']['usernameCanonical'] = $data['user']['username'] = $user->getUsername();
					$data['user']['email']             = $data['user']['emailCanonical'] = $data['email'] = $data['email'];
				}
				else
				{
					$data['user']['usernameCanonical'] = $data['user']['username'];
					$data['user']['email']             = $data['user']['emailCanonical'] = $data['email'];
				}
			}

			if ($form->get('user')->getData() instanceof User && !isset($data['user']) && $this->personManager->canDeleteUser($person))
			{
				$data['user'] = "";
				$form->remove('user');
				$form->add('user', HiddenType::class);
				if ($form->get('user')->getData()->getId() !== 1)
				{
					$this->om->remove($form->get('user')->getData());
					$flush = true;
				}
			}
		}

		// Address Management
		unset($data['address1_list'], $data['address2_list']);
		if (!empty($data['address1']) || !empty($data['address2']))
		{
			if ($data['address1'] == $data['address2'])
				$data['address2'] = "";
			elseif (empty($data['address1']) && !empty($data['address2']))
			{
				$data['address1'] = $data['address2'];
				$data['address2'] = "";
			}
		}

		// Email Management
		if (!empty($data['email']) || !empty($data['email2']))
		{
			if ($data['email'] == $data['email2'])
				$data['email2'] = "";
			elseif (empty($data['email']) && !empty($data['email2']))
			{
				$data['email']  = $data['email2'];
				$data['email2'] = "";
			}
		}


		//photo management
		if (empty($data['photo']))
		{
			$data['photo'] = $form->get('photo')->getNormData();
		}

		if ($flush)
			$this->om->flush();

		if (empty($data['preferredName']))
			$data['preferredName'] = $data['firstName'];

		if ($data['photo'] instanceof File && empty($data['photo']->getFilename()))
			$data['photo'] = null;

		$event->setData($data);
	}

	/**
	 * Add Staff Fields
	 *
	 * @param $form
	 */
	private function addStaffFields($form)
	{
		$form
			->add('staffType', SettingChoiceType::class, array(
					'label'        => 'staff.stafftype.label',
					'setting_name' => 'Staff.Categories',
					'placeholder'  => 'staff.stafftype.placeholder',
					'attr'         => array(
						'class' => 'staffMember',
					)
				)
			)
			->add('jobTitle', TextType::class, array(
					'label' => 'staff.jobTitle.label',
					'attr'  => array(
						'class' => 'staffMember',
					)
				)
			)
			->add('house', SettingChoiceType::class, array(
					'label'        => 'staff.house.label',
					'placeholder'  => 'staff.house.placeholder',
					'required'     => false,
					'attr'         => array(
						'help' => 'staff.house.help',
					),
					'setting_name' => 'house.list',
				)
			)/*			->add('homeroom', EntityType::class, array(
					'label'         => 'staff.label.homeroom',
					'class'         => Space::class,
					'choice_label'  => 'name',
					'placeholder'   => 'staff.placeholder.homeroom',
					'required'      => false,
					'attr'          => array(
						'help' => 'staff.help.homeroom',
					),
					'query_builder' => function (EntityRepository $er) use ($options) {
						return $er->createQueryBuilder('h')
							->leftJoin('h.staff', 's')
							->where('s.person = :person_id')
							->orWhere('h.staff IS NULL')
							->setParameter('person_id', $options['person_id'])
							->orderBy('h.name', 'ASC');
					},
				)
			)
*/
		;

	}

	/**
	 * Add Staff Fields
	 *
	 * @param $form
	 */
	private function addStudentFields($form)
	{
		$form
			->add('startAtSchool', DateType::class,
				[
					'years' => range(date('Y', strtotime('-25 years')), date('Y', strtotime('+1 year'))),
					'label' => 'student.startAtSchool.label',
					'attr'  => array(
						'help'  => 'student.startAtSchool.help',
						'class' => 'student',
					),
				]
			)
			->add('startAtThisSchool', DateType::class, array(
					'years' => range(date('Y', strtotime('-25 years')), date('Y', strtotime('+1 year'))),
					'label' => 'student.startAtThisSchool.label',
					'attr'  => array(
						'help'  => 'student.startAtThisSchool.help',
						'class' => 'student',
					),
				)
			)
			->add('lastAtThisSchool', DateType::class, array(
					'years'    => range(date('Y', strtotime('-5 years')), date('Y', strtotime('+18 months'))),
					'label'    => 'student.lastAtThisSchool.label',
					'attr'     => array(
						'help'  => 'student.lastAtThisSchool.help',
						'class' => 'student',
					),
					'required' => false,
				)
			)
			->add('firstLanguage', LanguageType::class, array(
					'label'       => 'student.language.first.label',
					'placeholder' => 'student.language.placeholder',
					'required'    => false,
				)
			)
			->add('secondLanguage', LanguageType::class, array(
					'label'       => 'student.language.second.label',
					'placeholder' => 'student.language.placeholder',
					'required'    => false,
				)
			)
			->add('thirdLanguage', LanguageType::class, array(
					'label'       => 'student.language.third.label',
					'placeholder' => 'student.language.placeholder',
					'required'    => false,
				)
			)
			->add('countryOfBirth', CountryType::class, array(
					'label'       => 'student.countryOfBirth.label',
					'placeholder' => 'student.countryOfBirth.placeholder',
					'required'    => false,
				)
			)
			->add('ethnicity', SettingChoiceType::class,
				array(
					'label'        => 'student.ethnicity.label',
					'placeholder'  => 'student.ethnicity.placeholder',
					'required'     => false,
					'setting_name' => 'Ethnicity.List',
				)
			)
			->add('religion', SettingChoiceType::class,
				array(
					'label'        => 'student.religion.label',
					'placeholder'  => 'student.religion.placeholder',
					'required'     => false,
					'setting_name' => 'Religion.List',
				)
			)
			->add('citizenship1', CountryType::class,
				array(
					'label'       => 'student.citizenship.1.label',
					'placeholder' => 'student.citizenship.placeholder',
					'required'    => false,
				)
			)
			->add('citizenship2', CountryType::class,
				array(
					'label'       => 'student.citizenship.2.label',
					'placeholder' => 'student.citizenship.placeholder',
					'required'    => false,
				)
			)
			->add('citizenship1Passport', TextType::class,
				array(
					'label'    => 'student.citizenship.passport.1.label',
					'required' => false,
				)
			)
			->add('citizenship2Passport', TextType::class,
				array(
					'label'    => 'student.citizenship.passport.2.label',
					'required' => false,
				)
			)
			->add('locker', TextType::class,
				array(
					'label'    => 'student.locker.label',
					'required' => false,
				)
			)
			->add('citizenship1PassportScan', ImageType::class, array(
					'attr'     => array(
						'help'       => 'student.passportScan.help',
						'imageClass' => 'headShot75',
					),
					'label'    => 'student.passportScan.label',
					'required' => false,
				)
			)
			->add('nationalIDCardNumber', TextType::class,
				[
					'label'    => 'student.nationalIDCardNumber.label',
					'required' => false,
				]
			)
			->add('nationalIDCardScan', ImageType::class, array(
					'attr'     => array(
						'help'       => 'student.nationalIDCardScan.help',
						'imageClass' => 'headShot75',
					),
					'label'    => 'student.nationalIDCardScan.label',
					'required' => false,
				)
			)
			->add('residencyStatus', SettingChoiceType::class,
				array(
					'label'        => 'student.residencyStatus.label',
					'placeholder'  => 'student.residencyStatus.placeholder',
					'required'     => false,
					'setting_name' => 'Residency.List',
					'attr'         => array(
						'help' => 'student.residencyStatus.help',
					),
				)
			)
			->add('visaExpiryDate', DateType::class, array(
					'years'    => range(date('Y', strtotime('-1 years')), date('Y', strtotime('+10 year'))),
					'label'    => 'student.visaExpiryDate.label',
					'attr'     => array(
						'help'  => 'student.visaExpiryDate.help',
						'class' => 'student',
					),
					'required' => false,
				)
			)
			->add('house', SettingChoiceType::class,
				[
					'label'                     => 'student.house.label',
					'placeholder'               => 'student.house.placeholder',
					'required'                  => false,
					'attr'                      => array(
						'help' => 'student.house.help',
					),
					'setting_name'              => 'house.list',
					'choice_translation_domain' => 'SystemBundle',
				]
			);
		if ($this->personManager->gradesInstalled())
		{
			$form->add('grades', CollectionType::class,
				[
					'label'         => 'student.grades.label',
					'allow_add'     => true,
					'allow_delete'  => true,
					'entry_type'    => StudentGradeType::class,
					'attr'          => [
						'class' => 'gradeList',
						'help'  => 'student.grades.help',
					],
					'entry_options' => [
						'systemYear' => $form->getConfig()->getOption('systemYear'),
					],
				]
			);
		}
	}
}