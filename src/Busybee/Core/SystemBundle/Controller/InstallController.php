<?php

namespace Busybee\Core\SystemBundle\Controller;

use Busybee\Core\CalendarBundle\Entity\Term;
use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\Core\SecurityBundle\Entity\User;
use Busybee\People\PersonBundle\Entity\Person;
use Busybee\People\StaffBundle\Entity\Staff;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class InstallController extends Controller
{
	public function buildAction()
	{
		$em    = $this->get('doctrine')->getManager();
		$newEm = EntityManager::create($em->getConnection(), $em->getConfiguration());

		$im = $this->get('install.manager');

		$this->entity = $newEm->getRepository(User::class)->find(1);
		if (is_null($this->entity))
			$this->entity = new User();
		$parameters = $im->getParameters();
		$user       = $im->getSystemUser();

		if (intval($this->entity->getId()) == 0 && !empty($user))
		{
			$this->entity->setUsername($user['username']);
			$this->entity->setUsernameCanonical($user['username']);
			$this->entity->setEmail($user['email']);
			$this->entity->setEmailCanonical($user['email']);
			$this->entity->setLocale('en_GB');
			$this->entity->setLocked(false);
			$this->entity->setExpired(false);
			$this->entity->setCredentialsExpired(false);
			$this->entity->setEnabled(true);
			$this->entity->setDirectroles(['ROLE_SYSTEM_ADMIN']);
			$this->entity->setCreatedBy($this->entity);
			$this->entity->setModifiedBy($this->entity);
			$encoder  = $this->get('security.password_encoder');
			$password = $encoder->encodePassword($this->entity, $user['password']);
			$this->entity->setPassword($password);
			$newEm->persist($this->entity);
			$newEm->flush();
		}


		$session = $this->get('session');

		$session->invalidate();

		$im->removeSystemUser();

		$im->saveParameters($parameters);

		$this->get('session')->getFlashBag()->add('success', 'buildDatabase.success');
		$user = $newEm->getRepository(User::class)->find(1);

		$token = new UsernamePasswordToken($user, null, "default", $user->getRoles());

		$this->get('security.token_storage')->setToken($token);

		return new RedirectResponse($this->generateUrl('install_build_complete'));
	}

	/**
	 * @return RedirectResponse
	 */
	public function completeAction()
	{
		$this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN');

		$user = $this->get('user.repository')->find(1);

		$newEm = $this->get('doctrine')->getManager();

		if (!$user->hasPerson())
		{
			$person = new Person();
			$person->setUser($user);
			$user->setPerson($person);
			$person->setEmail($user->getEmail());
			$person->setFirstName('System');
			$person->setSurname('Administrator');
			$person->setPreferredName('Sys.Ad.');
			$person->setOfficialName('System Administrator');
			$staff = new Staff();
			$staff->setPerson($person);
			$newEm->persist($person);
			$newEm->persist($staff);
			$newEm->flush();
		}

		$year = $this->get('current.year.currentYear');

		if (empty($year->getId()))
		{
			$year = new Year();
		}

		$year->setName(date('Y'));
		$year->setFirstDay(new \DateTime(date('Y') . '0101 00:00:00'));
		$year->setLastDay(new \DateTime(date('Y') . '1231 00:00:00'));
		$year->setStatus('Current');
		$newEm->persist($year);
		$newEm->flush();
		$term = new Term();
		$term->setYear($year);
		$term->setFirstDay($year->getFirstDay());
		$term->setLastDay($year->getLastDay());
		$term->setName('Term');
		$term->setNameShort('T');
		$newEm->persist($term);
		$newEm->flush();

		$this->get('session')->getFlashBag()->add('success', 'buildComplete.success');

		return new RedirectResponse($this->generateUrl('bundle_update', ['name' => 'All']));
	}
}