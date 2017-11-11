<?php

namespace Busybee\Core\SecurityBundle\Controller;

use Busybee\Core\SecurityBundle\Entity\User;
use Busybee\Core\SecurityBundle\Form\UserType;
use Busybee\Core\SystemBundle\Model\MessageManager;
use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Busybee\Core\SecurityBundle\Event\FormEvent;
use Busybee\Core\SecurityBundle\Event\GetResponseUserEvent;
use Busybee\Core\SecurityBundle\Event\FilterUserResponseEvent;
use Busybee\Core\SecurityBundle\BusybeeSecurityEvents;
use Busybee\Core\SecurityBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

class UserController extends BusyBeeController
{
	/**
	 * Tell the user his account is now confirmed
	 */
	public function confirmedAction()
	{
		$this->denyAccessUnlessGranted('ROLE_USER');

		$user = $this->getUser();

		return $this->render('BusybeeSecurityBundle:User:confirmed.html.twig', array(
			'user' => $user,
		));
	}

	/**
	 * Reset user password
	 *
	 * @param Request $request
	 * @param string  $token
	 *
	 * @return null|\Symfony\Component\HttpFoundation\Response
	 */
	public function resetAction(Request $request, $token)
	{

		/** @var $userManager \Busybee\Core\SecurityBundle\Model\UserManagerInterface */
		$userManager = $this->get('busybee_core_security.doctrine.user_manager');
		/** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
		$dispatcher = $this->get('event_dispatcher');

		$user = $userManager->findUserByConfirmationToken($token);


		if (null === $user)
		{
			throw new TokenNotFoundException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
		}

		$event = new GetResponseUserEvent($user, $request);
		$dispatcher->dispatch(BusybeeSecurityEvents::RESETTING_RESET_INITIALISE, $event);

		if (null !== $event->getResponse())
		{
			return $event->getResponse();
		}

		$form = $this->createForm('Busybee\Core\SecurityBundle\Form\ResetType', $user);
		$form->setData($user);

		$form->handleRequest($request);

		$validator = $this->get('validator');

		$constraints = array(
			new Password(),
		);

		$errors = $validator->validate($request->request->get('reset')['plainPassword']['first'], $constraints);

		$valid = true;
		if (count($errors) > 0)
		{
			$this->get('session')->getFlashBag()->add('error', $errors->get(0)->getMessage());
			$valid = false;
		}

		if ($valid && $form->isValid())
		{
			$event = new FormEvent($form, $request);
			$dispatcher->dispatch(BusybeeSecurityEvents::RESETTING_RESET_SUCCESS, $event);

			$user->setConfirmationToken(null);
			$user->setPasswordRequestedAt(null);
			$userManager->updateUser($user);

			if (null === $response = $event->getResponse())
			{
				$this->addFlash(
					'success',
					$this->get('translator')->trans('user.reset.success', array(), 'BusybeeSecurityBundle')
				);
				$response = $this->redirectToRoute('/');
			}

			$dispatcher->dispatch(BusybeeSecurityEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

			return $response;
		}
		$config                 = new \stdClass();
		$config->misc           = new \stdClass();
		$config->misc->password = $this->get('busybee_core_system.password.password_manager')->buildPassword($this->getParameter('password'));

		return $this->render('BusybeeSecurityBundle:User:reset.html.twig', array(
			'token'  => $token,
			'config' => $config,
			'form'   => $form->createView(),
		));
	}

	/**
	 * Request reset user password: show form
	 *
	 * @param integer $id
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function requestAction($id, Request $request)
	{
		if (intval($id) > 0)
			$this->denyAccessUnlessGranted('ROLE_REGISTRAR');

		$user  = intval($id) > 0 ? $this->get('busybee_core_security.repository.user_repository')->find($id) : $this->getUser();
		$email = null;
		$force = false;
		if (!empty($user))
		{
			$email = trim($user->getEmail());
			$force = $user->getExpired();
		}

		if ($user->getConfirmationToken() && $user->isPasswordRequestNonExpired($this->container->getParameter('busybee_security.resetting.token_ttl')))
		{
			return $this->render('@BusybeeSecurity/User/passwordAlreadyRequested.html.twig', ['user' => $user]);
		}

		$config         = new \stdClass();
		$config->signin = $this->get('busybee_core_security.repository.failure_repository')->testRemoteAddress($request->server->get('REMOTE_ADDR'));

		return $this->render('BusybeeSecurityBundle:User:request.html.twig', array(
			'email'              => $email,
			'config'             => $config,
			'forcePasswordReset' => $force,
		));
	}

	/**
	 * Request reset user password: submit form and send email
	 *
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function sendEmailAction(Request $request)
	{
		$username = $request->request->get('username');
		if (empty($username))
			$username = $request->request->get('email');

		/** @var $user UserInterface */
		$user = $this->get('busybee_core_security.doctrine.user_manager')->findUserByUsernameOrEmail($username);

		if (null === $user)
		{
			$config         = new \stdClass();
			$config->signin = $this->get('busybee_core_security.repository.failure_repository')->testRemoteAddress($request->server->get('REMOTE_ADDR'));

			return $this->render('BusybeeSecurityBundle:User:request.html.twig', array(
				'invalid_username'   => $username,
				'config'             => $config,
				'forcePasswordReset' => false,
			));
		}

		if ($user->isPasswordRequestNonExpired($this->container->getParameter('busybee_security.resetting.token_ttl')))
		{
			return $this->render('BusybeeSecurityBundle:User:passwordAlreadyRequested.html.twig', ['user' => $user]);
		}

		if (null === $user->getConfirmationToken())
		{
			/** @var $tokenGenerator \Busybee\Core\SecurityBundle\Util\TokenGeneratorInterface */
			$tokenGenerator = $this->get('busybee_core_security.util.token_generator');
			$user->setConfirmationToken($tokenGenerator->generateToken());
		}

		$comment = $request->get('comment');

		$this->get('busybee_core_security.mailer.mailer')->sendResettingEmailMessage($user, $comment);
		$user->setPasswordRequestedAt(new \DateTime());
		$this->get('busybee_core_security.doctrine.user_manager')->updateUser($user);

		return $this->redirectToRoute('busybee_security_user_reset_check_email', ['email' => $this->getObfuscatedEmail($user)]);
	}

	/**
	 * Get the truncated email displayed when requesting the resetting.
	 *
	 * The default implementation only keeps the part following @ in the address.
	 *
	 * @param UserInterface $user
	 *
	 * @return string
	 */
	protected function getObfuscatedEmail(UserInterface $user)
	{
		$email = $user->getEmail();
		if (false !== $pos = strpos($email, '@'))
		{
			$email = '...' . substr($email, $pos);
		}

		return $email;
	}

	/**
	 * Create a new User from the Person Record.
	 *
	 * @param integer $personID
	 *
	 * @return RedirectResponse
	 */
	public function createAction($personID)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR');

		$person = $this->get('busybee_people_person.repository.person_repository')->find($personID);
		if (empty($person->getUser()))
		{
			$userManager = $this->get('busybee_core_security.doctrine.user_manager');
			$user        = $userManager->createUser();
			$user->setEnabled(true);
			$user->setEmail($person->getEmail());
			$user->setplainPassword('p@ssword');
			$userManager->updateUser($user);

			$person->setUser($user);

			$em = $this->getDoctrine()->getManager();

			$em->persist($person);
			$em->flush();

		}

		return $this->redirectToRoute('person_manage', ['personID' => $person->getId()]);
	}

	/**
	 * Tell the user to check his email provider
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function checkEmailAction(Request $request)
	{
		$email = $request->query->get('email');

		if (empty($email))
		{
			// the user does not come from the sendEmail action
			return $this->redirectToRoute('busybee_security_resetting_request');
		}


		return $this->render('BusybeeSecurityBundle:User:checkEmail.html.twig', array(
			'email' => $email,
		));
	}

	/**
	 * toggle User Enabled
	 *
	 * @param integer $id
	 *
	 * @return JsonResponse
	 */
	public function toggleEnabledAction($id)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR');

		$userManager = $this->get('busybee_core_security.repository.user_repository');

		$user = $userManager->find($id);
		$user->setEnabled(!$user->getEnabled());
		$em = $this->getDoctrine()->getManager();
		$em->persist($user);
		$em->flush();
		$enabled = $user->getEnabled() ? 'enabled' : 'disabled';
		$status  = $user->getEnabled() ? 'success' : 'warning';

		return new JsonResponse(
			array('message' => $this->get('translator')->trans('The user %user% was ' . $enabled, array('%user%' => $user->formatName()), 'BusybeeSecurityBundle'), 'status' => $status),
			200
		);
	}

	/**
	 * @param Request $request
	 * @param         $id
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function editAction(Request $request, $id)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR');

		$em = $this->get('doctrine')->getManager();

		if ($id === 'Add')
			$entity = new User();
		elseif ($id === 'Current')
		{
			$entity = $this->getUser();
			$id     = $entity->getId();
		}
		else
			$entity = $em->getRepository(User::class)->find($id);

		$person_id = $this->get('busybee_core_security.doctrine.user_manager')->personExists($entity);
		{
			return $this->redirectToRoute('person_edit', ['id' => $person_id, '_fragment' => 'user']);
		}

		$form = $this->createForm(UserType::class, $entity, ['isSystemAdmin' => $this->isGranted('ROLE_SYSTEM_ADMIN')]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{

			$em->persist($entity);
			$em->flush();
			if ($id === 'Add')
				$this->redirectToRoute('security_user_edit', ['id' => $entity->getId()]);
		}

		return $this->render('@BusybeeSecurity/User/user.html.twig', ['form' => $form->createView()]);
	}

	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function listAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR');

		if ($this->get('busybee_core_security.doctrine.user_manager')->personExists(null))
		{
			$pagin = $this->get('session')->get('pagination');

			$person           = [];
			$person['limit']  = 10;
			$person['search'] = '';
			$person['offSet'] = 0;
			$person['choice'] = $this->generateUrl('user_manage');
			$person['sortBy'] = "person.surname.label";
			$pagin['Person']  = $person;

			$this->get('session')->set('pagination', $pagin);

			return $this->redirectToRoute('user_manage');
		}
		$up = $this->get('busybee_core_security.model.user_pagination');

		$up->injectRequest($request);

		$up->getDataSet();

		return $this->render('@BusybeeSecurity/User/list.html.twig', ['pagination' => $up,]);
	}

	/**
	 * @param $id
	 *
	 * @return RedirectResponse
	 */
	public function deleteAction($id)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR');

		$em = $this->get('doctrine')->getManager();


		$mm = new MessageManager();
		$mm->setDomain('BusybeeSecurityBundle');

		$entity = $em->getRepository(User::class)->find($id);

		if (!$entity->canDelete())
			$mm->addMessage('warning', 'security.user.delete.enabled', ['%name%' => $entity->getUsername()]);
		else
		{
			try
			{
				$name = $entity->getUsername();
				$em->remove($entity);
				$em->flush();
				$mm->addMessage('success', 'security.user.delete.success', ['%name%' => $name]);
			}
			catch (\Exception $e)
			{
				$mess = $e->getMessage();
				if ($this->get('kernel')->getEnvironment() == 'Prod')
					$mess = '';
				$mm->addMessage('danger', 'security.user.delete.database', ['%message%' => $mess]);
			}
		}

		$this->get('busybee_core_system.model.flash_bag_manager')->addMessages($mm);

		return $this->redirectToRoute('security_user_list');

	}
}
