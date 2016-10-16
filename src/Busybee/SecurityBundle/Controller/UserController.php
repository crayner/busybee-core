<?php

namespace Busybee\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Busybee\SecurityBundle\Form\UserType;
use Busybee\SecurityBundle\Form\RegisterType;
use Busybee\SecurityBundle\Form\ResetType;
use Symfony\Component\HttpFoundation\RedirectResponse ;
use Busybee\SecurityBundle\Event\FormEvent;
use Busybee\SecurityBundle\Event\GetResponseUserEvent;
use Busybee\SecurityBundle\Event\FilterUserResponseEvent;
use Busybee\SecurityBundle\BusybeeSecurityEvents;
use Busybee\SecurityBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse ;
use Busybee\DisplayBundle\Model\MessageManager ;

class UserController extends Controller
{
   /**
     * Register a new User
     */
    public function registerAction(Request $request) {
    
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_REGISTRAR'))) return $response;
		
        $userManager = $this->get('busybee_security.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();        
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(BusybeeSecurityEvents::REGISTRATION_INITIALISE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

     
        $form = $this->createForm( 'Busybee\SecurityBundle\Form\RegisterType', $user);
        $form->add('locked', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
                 	'data' 					=> 0
            	)
			)
			->add('enabled', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                 	'data' 					=> true,
					'label' 				=> 'user.label.enabled',
            	)
			)
			->add('expired', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
                 	'data' 					=> 0,
            	)
			)
			->add('credentials_expired', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
                 	'data' 					=> 0,
            	)
			)
			->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
					'label'					=> 'form.cancel', 
					'translation_domain' 	=> 'BusybeeDisplayBundle',
					'attr' 					=> array(
						'formnovalidate' 		=> 'formnovalidate',
						'class' 				=> 'btn btn-info glyphicon glyphicon-exclamation-sign',
						'onClick'				=> 'location.href=\''.$this->generateUrl('busybee_security_user_list')."'",
					),
				)
			);


        $form->setData($user);

        $form->handleRequest($request);

		$mm = new MessageManager($this->get('translator'), $request->getLocale(), 'BusybeeSecurityBundle');

        if ($form->isSubmitted()) {
			if ($form->isValid()) {

				$event = new FormEvent($form, $request);

				$dispatcher->dispatch(BusybeeSecurityEvents::REGISTRATION_SUCCESS, $event);
				
				$repository = $this->get('busybee_security.user_provider.username');
				
				$userManager->updateUser($user);
				
				if (null === $response = $event->getResponse()){
					$url = $this->generateUrl('busybee_home_page');
					$response = new RedirectResponse($url);
				}

				$dispatcher->dispatch(BusybeeSecurityEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

				return new JsonResponse(
					array(
						'message' => $mm->message('user.success.registration', 'success'),
						'form' => $this->renderView('BusybeeSecurityBundle:User:register_content.html.twig',
							array(
								'user' => $user,
								'form' => $form->createView(),
								)
							)
						), 
						200);
				
			} else {
			
				return new JsonResponse(
					array(
						'message' => $mm->message('user.error.registration', 'danger'),
						'form' => $this->renderView('BusybeeSecurityBundle:User:register_content.html.twig',
							array(
								'user' => $user,
								'form' => $form->createView(),
								)
							)
						), 
						400);

			}
		}
        
        return $this->render('BusybeeSecurityBundle:User:register.html.twig', array(
                'form' => $form->createView(),
            ));
    }
    /**
     * @return array
     */
    private function getGroupList()
    {
        $groupRepo = $this->getDoctrine()
            ->getRepository('BusybeeSecurityBundle:Group');

        $groupList = $groupRepo->findAll();
        $x = array();
        foreach($groupList as $q=>$group) {
            $x[$group->getId()] = $group->getGroupname();
        }
        return $x;
    }    

    /**
     * Tell the user his account is now confirmed
     */
    public function confirmedAction()
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_USER'))) return $response;
		
        $user = $this->getUser();

        return $this->render('BusybeeSecurityBundle:User:confirmed.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Edit the user
     */
    public function editAction(Request $request)
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_USER'))) return $response;
		
        $user = $this->getUser();
    

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(BusybeeSecurityEvents::USER_EDIT_INITIALISE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \Busybee\SecurityBundle\Form\Factory\FactoryInterface */
        $form = $this->createForm( 'Busybee\SecurityBundle\Form\UserType', $user);
        $form->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
					'label'					=> 'form.cancel', 
					'translation_domain' 	=> 'BusybeeDisplayBundle',
					'attr' 					=> array(
						'formnovalidate' 		=> 'formnovalidate',
						'class' 				=> 'btn btn-info glyphicon glyphicon-exclamation-sign',
						'onClick'				=> 'location.href=\''.$this->generateUrl('busybee_home_page')."'",
					),
				)
			)
		;
		
        $form->setData($user);

        $form->handleRequest($request);

		$mm = new MessageManager($this->get('translator'), $request->getLocale(), 'BusybeeSecurityBundle');
		
		if ($form->isSubmitted()) {
			if ($form->isValid()) {

				/** @var $userManager \Busybee\SecurityBundle\Model\UserManagerInterface */
				$userManager = $this->get('busybee_security.user_manager');
	
				$event = new FormEvent($form, $request);
				$dispatcher->dispatch(BusybeeSecurityEvents::USER_EDIT_SUCCESS, $event);
	
				$userManager->updateUser($user);
	
				if (null === $response = $event->getResponse()){
					$url = $this->generateUrl('busybee_home_page');
					$response = new RedirectResponse($url);
				}

				$dispatcher->dispatch(BusybeeSecurityEvents::USER_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

				return new JsonResponse(
					array(
						'message' => $mm->message('user.edit.success', 'success'),
						'form' => $this->renderView('BusybeeSecurityBundle:User:edit_content.html.twig',
							array(
								'user' => $user,
								'form' => $form->createView(),
								)
							)
						), 
						200);
	
			} else {

				return new JsonResponse(
					array(
						'message' => $mm->message('user.edit.error', 'danger'),
						'form' => $this->renderView('BusybeeSecurityBundle:User:edit_content.html.twig',
							array(
								'user' => $user,
								'form' => $form->createView(),
								)
							)
						), 
						400);
			}
		}

        return $this->render('BusybeeSecurityBundle:User:edit.html.twig', array(
				'user' => $user,
				'form' => $form->createView(),
			)
		);
    }
    /**
     * @retrun array
     */
    private function userRoleList() 
    { 
        $roleHierarchy = $this->get('security.role_hierarchy');
        $roleList = array();
        foreach($roleHierarchy->getHierarchy() as $role => $w)
            if ($this->get('security.authorization_checker')->isGranted($role))
                $roleList[$role] = $role;
        return $roleList;
    }
    /**
     * Impersonate the user
     */
    public function impersonateAction()
    {

		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_ALLOWED_TO_SWITCH'))) return $response;
		
        $user = $this->getUser();

        $roleHierarchy = $this->get('security.role_hierarchy');

        $userManager = $this->get('busybee_security.user_manager');

		$users = $userManager->findChildren($user, $this->get('security.authorization_checker'));

		if ( empty ( $users ) ) {
            $this->addFlash(
                'warning',
                $this->get('translator')->trans('No users where found that you have permission to impersonate.')
            );
		}
		
        return $this->render('BusybeeSecurityBundle:User:impersonate.html.twig', array(
            'user' => $user,
			'users' => $users,
        ));
    }

    /**
     * Reset user password
     */
    public function resetAction(Request $request, $token)
    {

        /** @var $userManager \Busybee\SecurityBundle\Model\UserManagerInterface */
        $userManager = $this->get('busybee_security.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);


        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(BusybeeSecurityEvents::RESETTING_RESET_INITIALISE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

		$form = $this->createForm(new ResetType, $user);
        $form->setData($user);

        $form->handleRequest($request);
		if ($form->get('cancel')->isClicked()) {
			$url = $this->generateUrl('busybee_home_page');
			$response = new RedirectResponse($url);
			return $response;			
		}

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(BusybeeSecurityEvents::RESETTING_RESET_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
				$this->addFlash(
					'success',
					$this->get('translator')->trans('user.reset.success', array(), 'BusybeeSecurityBundle')
				);
                $url = $this->generateUrl('busybee_home_page');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(BusybeeSecurityEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('BusybeeSecurityBundle:User:reset.html.twig', array(
            'token' => $token,
            'form' => $form->createView(),
        ));
    }

    /**
     * Request reset user password: show form
     */
    public function requestAction(Request $request)
    {
        $user = $this->getUser();
		$email = null;
		if (!empty($user))
			$email = trim($user->getEmail());
		
		$config = new \stdClass();
		$config->signin = $this->get('security.failure.repository')->testRemoteAddress($request->server->get('REMOTE_ADDR'));
		
		return $this->render('BusybeeSecurityBundle:User:request.html.twig', array(
			'email' => $email,
			'config' => $config,
		));
    }

    /**
     * Request reset user password: submit form and send email
     */
    public function sendEmailAction(Request $request)
    {
        $username = $request->request->get('username');
		if (empty($username))
        	$username = $request->request->get('email');

        /** @var $user UserInterface */
        $user = $this->get('busybee_security.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
			$config = new \stdClass();
			$config->signin = $this->get('security.failure.repository')->testRemoteAddress($request->server->get('REMOTE_ADDR'));
		
            return $this->render('BusybeeSecurityBundle:User:request.html.twig', array(
                'invalid_username' => $username,
				'config' => $config,
            ));
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('busybee_security.resetting.token_ttl'))) {
            return $this->render('BusybeeSecurityBundle:User:passwordAlreadyRequested.html.twig');
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \Busybee\SecurityBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->get('busybee_security.util.tokengenerator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->get('busybee_security.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->get('busybee_security.user_manager')->updateUser($user);

        return new RedirectResponse($this->generateUrl('busybee_security_user_reset_check_email',
            array('email' => $this->getObfuscatedEmail($user))
        ));
    }

    /**
     * Get the truncated email displayed when requesting the resetting.
     *
     * The default implementation only keeps the part following @ in the address.
     *
     * @param \Busybee\SecurityBundle\Model\UserInterface $user
     *
     * @return string
     */
    protected function getObfuscatedEmail(UserInterface $user)
    {
        $email = $user->getEmail();
        if (false !== $pos = strpos($email, '@')) {
            $email = '...' . substr($email, $pos);
        }

        return $email;
    }

   /**
     * Create a new User from the Person Record.
     */
    public function createAction($personID) {
    
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_ADMIN'))) return $response;
		$personRepo = $this->get('busybee_people.repository');
		$person = $personRepo->find($personID);
        if (empty($person->getUser())) {
			$userManager = $this->get('busybee_security.user_manager');
			$user = $userManager->createUser();        
			$user->setEnabled(true);
			$user->setEmail($person->getEmail());
			$user->setplainPassword('p@ssword');
			$userManager->updateUser($user);

			$person->setUser($user);
			
			$em = $this->getDoctrine()->getManager();
				
			$em->persist($person);
			$em->flush();

		}
		
        return new RedirectResponse($this->generateUrl('busybee_people_manage',
				array('personID' => $person->getId())
			)
		);
		
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction(Request $request)
    {
        $email = $request->query->get('email');

        if (empty($email)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->generateUrl('busybee_user_resetting_request'));
        }

        return $this->render('BusybeeSecurityBundle:User:checkEmail.html.twig', array(
            'email' => $email,
        ));
    }


}
