<?php

namespace Busybee\SecurityBundle\Controller;

use Busybee\FormBundle\Type\ToggleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse ;
use Busybee\SecurityBundle\Event\FormEvent;
use Busybee\SecurityBundle\Event\GetResponseUserEvent;
use Busybee\SecurityBundle\Event\FilterUserResponseEvent;
use Busybee\SecurityBundle\BusybeeSecurityEvents;
use Busybee\SecurityBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse ;
use Busybee\HomeBundle\Model\MessageManager ;
use Busybee\SecurityBundle\Validator\Password ;
use Symfony\Component\Form\FormError ;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException ;

class UserController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted ;

    /**
     * Register a new User
     */
    public function registerAction(Request $request) {

        $this->denyAccessUnlessGranted('ROLE_REGISTRAR');

        $userManager = $this->get('busybee_security.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $cUser = $this->getUser();

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
            ->add('enabled', ToggleType::class, array(
                    'data' 					=> true,
                    'label'					=> 'register.enabled.label',
                    'attr'					=> array(
                        'help'					=> 'register.enabled.description',
                    ),
                )
            )
            ->add('expired', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
                    'data' 					=> 0,
                )
            )
            ->add('locale', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
                    'data' 					=> $this->getParameter('locale'),
                )
            )
            ->add('credentials_expired', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
                    'data' 					=> 0,
                )
            )
            ->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
                    'label'					=> 'form.cancel',
                    'translation_domain' 	=> 'BusybeeHomeBundle',
                    'attr' 					=> array(
                        'formnovalidate' 		=> 'formnovalidate',
                        'class' 				=> 'btn btn-info glyphicons glyphicons-remove-circle',
                        'onClick'				=> 'location.href=\''.$this->generateUrl('home_page')."'",
                    ),
                )
            );

        $form->setData($user);

        $form->handleRequest($request);

        $mm = new MessageManager($this->get('translator'), $request->getLocale(), 'BusybeeSecurityBundle');

        $password = $this->get('system.password.manager')->buildPassword($this->getParameter('password'));

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $event = new FormEvent($form, $request);

                $dispatcher->dispatch(BusybeeSecurityEvents::REGISTRATION_SUCCESS, $event);

                $user->setPlainPassword($request->request->get('password1'));

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()){
                    $url = $this->generateUrl('home_page');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(BusybeeSecurityEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($cUser, $request, $response));

                return new JsonResponse(
                    array(
                        'message' => $mm->message('user.success.registration', 'success'),
                        'form' => $this->renderView('BusybeeSecurityBundle:User:register_content.html.twig',
                            array(
                                'user' => $user,
                                'form' => $form->createView(),
                                'password' => $password,
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
                                'password' => $password,
                            )
                        )
                    ),
                    400);

            }
        }

        return $this->render('BusybeeSecurityBundle:User:register.html.twig', array(
            'form' => $form->createView(),
            'password' => $password,
        ));
    }

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
     * Edit the user
     */
    public function editAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $config = new \stdClass();
        $config->signin = true ;

        $user = intval($id) > 0 ? $this->get('user.repository')->findOneBy(array('id'=>$id)) : $this->getUser();

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(BusybeeSecurityEvents::USER_EDIT_INITIALISE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createForm( 'Busybee\SecurityBundle\Form\UserType', $user);
        $form->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
                'label'					=> 'form.cancel',
                'translation_domain' 	=> 'BusybeeHomeBundle',
                'attr' 					=> array(
                    'formnovalidate' 		=> 'formnovalidate',
                    'class' 				=> 'btn btn-info glyphicons glyphicons-exclamation-sign',
                    'onClick'				=> 'location.href=\''.$this->generateUrl('home_page')."'",
                ),
            )
        );
        if ($id > 0)
            $form->add('enabled', ToggleType::class, array(
                    'label'					=> 'register.enabled.label',
                    'attr'					=> array(
                        'help'					=> 'register.enabled.description',
                    ),
                    'data'					=> true,
                )
            );
        else
            $form->add('enabled', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array('data' => true));

        $form->setData($user);

        $form->handleRequest($request);

        if ($form->get('username')->getData() == 'admin')
            $form->get('username')->addError(new FormError('user.edit.admin'));

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                /** @var $userManager \Busybee\SecurityBundle\Model\UserManagerInterface */
                $userManager = $this->get('busybee_security.user_manager');

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(BusybeeSecurityEvents::USER_EDIT_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()){
                    $url = $this->generateUrl('home_page');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(BusybeeSecurityEvents::USER_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                $this->get('session')->getFlashBag()->add('success', 'user.edit.success');
                return new JsonResponse(
                    array(
                        'form' => $this->renderView('BusybeeSecurityBundle:User:edit_content.html.twig',
                            array(
                                'user' => $user,
                                'form' => $form->createView(),
                            )
                        )
                    ),
                    200);

            } else {

                $this->get('session')->getFlashBag()->add('error', 'user.edit.error');
                return new JsonResponse(
                    array(
                        'form' => $this->renderView('BusybeeSecurityBundle:User:edit_content.html.twig',
                            array(
                                'user' => $user,
                                'form' => $form->createView(),
                            )
                        )
                    ),
                    200);
            }
        }

        return $this->render('BusybeeSecurityBundle:User:edit.html.twig', array(
                'user' => $user,
                'form' => $form->createView(),
                'config' => $config,
            )
        );
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

        $form = $this->createForm('Busybee\SecurityBundle\Form\ResetType', $user);
        $form->setData($user);

        $form->handleRequest($request);

        $validator = $this->get('validator');

        $constraints = array(
            new Password(),
        );

        $errors = $validator->validate($request->request->get('reset')['plainPassword']['first'], $constraints);

        $valid = true ;
        if (count($errors) > 0) {
            $this->get('session')->getFlashBag()->add('error', $errors->get(0)->getMessage());
            $valid = false;
        }

        if ($form->get('cancel')->isClicked()) {
            $url = $this->generateUrl('home_page');
            $response = new RedirectResponse($url);
            return $response;
        }

        if ($valid && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(BusybeeSecurityEvents::RESETTING_RESET_SUCCESS, $event);

            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);
            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $this->addFlash(
                    'success',
                    $this->get('translator')->trans('user.reset.success', array(), 'BusybeeSecurityBundle')
                );
                $url = $this->generateUrl('home_page');
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
    public function requestAction($id, Request $request)
    {
        if (intval($id) > 0)
            $this->denyAccessUnlessGranted('ROLE_REGISTRAR');

        $user = intval($id) > 0 ? $this->get('user.repository')->find($id) : $this->getUser();
        $email = null;
        $force = false ;
        if (! empty($user)) {
            $email = trim($user->getEmail());
            $force = $user->getExpired();
        }

        $config = new \stdClass();
        $config->signin = $this->get('security.failure.repository')->testRemoteAddress($request->server->get('REMOTE_ADDR'));

        return $this->render('BusybeeSecurityBundle:User:request.html.twig', array(
            'email' => $email,
            'config' => $config,
            'forcePasswordReset' => $force,
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
                'forcePasswordReset' => false,
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

        $this->denyAccessUnlessGranted('ROLE_REGISTRAR');

        $person = $this->get('person.repository')->find($personID);
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

        return new RedirectResponse($this->generateUrl('person_manage',
            array(
                'personID'      => $person->getId(),
                'currentSearch' => '',
            )
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
            return new RedirectResponse($this->generateUrl('busybee_security_resetting_request'));
        }


        return $this->render('BusybeeSecurityBundle:User:checkEmail.html.twig', array(
            'email' => $email,
        ));
    }

    /**
     * list the Users
     */
    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR');

        $up = $this->get('user.pagination');

        $up->injectRequest($request);

        $up->getDataSet();

        return $this->render('BusybeeSecurityBundle:User:list.html.twig',
            array(
                'pagination' => $up,
            )
        );
    }

    /**
     * toggle User Enabled
     */
    public function toggleEnabledAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR');

        $userManager = $this->get('user.repository');

        $user = $userManager->find($id);
        $user->setEnabled(! $user->getEnabled());
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        $enabled = $user->getEnabled() ? 'enabled' : 'disabled' ;
        $status = $user->getEnabled() ? 'success' : 'warning';

        return new JsonResponse(
            array('message' => $this->get('translator')->trans('The user %user% was '.$enabled, array('%user%' => $user->getFormatName()), 'BusybeeSecurityBundle'), 'status' => $status),
            200
        );
    }
}
