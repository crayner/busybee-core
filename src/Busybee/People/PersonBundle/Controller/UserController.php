<?php
namespace Busybee\People\PersonBundle\Controller;

use Busybee\Core\SecurityBundle\Entity\User;
use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Busybee\People\PersonBundle\Entity\Person;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends BusybeeController
{
	/**
	 * @param $id
	 *
	 * @return JsonResponse
	 */
	public function toggleAction($id)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$personManager = $this->get('busybee_people_person.model.person_manager');

		$person = $personManager->find($id);

		$user = $person->getUser();

		$om = $this->get('doctrine')->getManager();

		// Remove User from person

		if ($user instanceof User && $personManager->canDeleteUser())
		{
			$person->setUser(null);
			$om->persist($person);
			$om->flush();

			return new JsonResponse(
				array(
					'message' => '<div class="alert alert-success fadeAlert">' . $this->get('translator')->trans('user.toggle.removeSuccess', array('%name%' => $user->formatName()), 'BusybeePersonBundle') . '</div>',
					'status'  => 'removed',
				),
				200
			);
		}

		if (!empty($person->getEmail()) && $personManager->canBeUser())
		{
			$user = $om->getRepository(User::class)->findOneByEmail($person->getEmail());

			if (is_null($user) && !empty($person->getEmail2()))
				$user = $om->getRepository(User::class)->findOneByEmail($person->getEmail2());

			if (is_null($user))
			{
				$user = new User();
				$user->setEmail($person->getEmail());
				$user->setEmailCanonical($person->getEmail());
				$user->setUsername($person->getEmail());
				$user->setUsernameCanonical($person->getEmail());
				$user->setLocale($this->getParameter('locale'));
				$user->setPassword(password_hash(uniqid(), PASSWORD_BCRYPT));
				$user->setCredentialsExpired(true);
			}
			$user->setEnabled(true);
			$user->setLocked(false);
			$user->setExpired(false);
			$user->setCredentialsExpireAt(null);
			$user->setExpiresAt(null);

			$person->setUser($user);
			$om->persist($person);
			$om->flush();

			return new JsonResponse(
				array(
					'message' => '<div class="alert alert-success fadeAlert">' . $this->get('translator')->trans('user.toggle.addSuccess', array('%name%' => $user->formatName()), 'BusybeePersonBundle') . '</div>',
					'status'  => 'added',
				),
				200
			);

		}

		return new JsonResponse(
			array(
				'message' => '<div class="alert alert-warning fadeAlert">' . $this->get('translator')->trans('user.toggle.notUser', array('%name%' => $person->formatName()), 'BusybeePersonBundle') . '</div>',
				'status'  => 'failed',
			),
			200
		);
	}
}