<?php

namespace Busybee\People\PersonBundle\Events;

use Busybee\Core\TemplateBundle\Model\TabManager;
use Busybee\People\PersonBundle\Form\UserType;
use Busybee\People\PersonBundle\Model\PersonManager;
use Busybee\Core\SecurityBundle\Entity\User;
use Busybee\People\StaffBundle\Entity\Staff;
use Busybee\People\StaffBundle\Form\StaffType;
use Busybee\People\StudentBundle\Entity\Student;
use Busybee\People\StudentBundle\Form\StudentType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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

		if (!$person->isStaff())
			$form->remove('staff');

		if (!$person->isStudent())
			$form->remove('student');

		if (!$person->isUser())
			$form->remove('user');
		else
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
		if (is_null($data['photo']))
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
}