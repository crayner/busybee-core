<?php
namespace Busybee\Core\SystemBundle\Controller;

use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\Core\SecurityBundle\Entity\User;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class InstallController extends BusybeeController
{
	/**
	 * @param int $count
	 *
	 * @return RedirectResponse
	 */
	public function buildAction()
	{

		$em    = $this->get('doctrine')->getManager();

		$im = $this->get('busybee_core_template.model.install_manager');

		try
		{
			$user = $em->getRepository(User::class)->find(1);

		} catch (MappingException $e) {

			sleep(5);

			return new RedirectResponse($this->generateUrl('install_build_system'));
		}

		$parameters = $im->getParameters();
		$userDetails       = $im->getSystemUser();

		$user->setInstaller(true);
		$user->setUsername($userDetails['username']);
		$user->setUsernameCanonical($userDetails['username']);
		$user->setEmail($userDetails['email']);
		$user->setEmailCanonical($userDetails['email']);
		$user->setLocale('en_GB');
		$user->setLocked(false);
		$user->setExpired(false);
		$user->setCredentialsExpired(false);
		$user->setEnabled(true);
		$user->setDirectroles(['ROLE_SYSTEM_ADMIN']);
		$encoder  = $this->get('security.password_encoder');
		$password = $encoder->encodePassword($user, $userDetails['password']);
		$user->setPassword($password);

		$em->persist($user);
		$em->flush();

		$session = $this->get('session');

		$session->invalidate();

		$im->removeSystemUser();

		$im->saveParameters($parameters);

		$this->get('session')->getFlashBag()->add('success', 'buildDatabase.success');

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

		$em    = $this->get('doctrine')->getManager();

		try
		{
			$year = $em->getRepository(Year::class)->find(1);

		} catch (MappingException $e) {

			sleep(5);

			return new RedirectResponse($this->generateUrl('install_build_complete'));
		}

		if (is_null($year))
		{
			$year = new Year();
			$hemi = $this->getParameter('hemisphere');
			$hemi = $hemi === 'North' ? 'North' : 'South';
			$today = new \DateTime('now');
			$start = new \DateTime($today->format('Y').'0101');
			$finish = new \DateTime($today->format('Y').'1231');
			$name = $today->format('Y');
			if ($hemi === 'North'){
				if ($start->format('n') > '6')
				{
					$start = new \DateTime($today->format('Y').'0701');
					$finish = new \DateTime(($today->format('Y') + 1).'0630');
					$name = $today->format('Y') . ' - ' . ($today->format('Y') + 1);
				} else {
					$start = new \DateTime(($today->format('Y') - 1).'0701');
					$finish = new \DateTime($today->format('Y').'0630');
					$name = ($today->format('Y') - 1) . ' - ' . $today->format('Y');
				}
			}
			$year->setFirstDay($start);
			$year->setLastDay($finish);
			$year->setName($name);
			$year->setStatus('current');
			$em->persist($year);
			$em->flush();
		}

		$user = $this->get('busybee_core_security.repository.user_repository')->find(1);

		$user->setCreatedBy($user);
		$user->setModifiedBy($user);
		$user->setYear($year);

		$om = $this->get('doctrine')->getManager();
		$om->persist($user);
		$om->flush();

		$this->get('session')->getFlashBag()->add('success', 'buildComplete.success');

		return new RedirectResponse($this->generateUrl('bundle_update', ['name' => 'All']));
	}
}