<?php

namespace Busybee\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse ;
use Busybee\SecurityBundle\Event\FormEvent;
use Busybee\SecurityBundle\Event\GetResponseUserEvent;
use Busybee\SecurityBundle\Event\FilterUserResponseEvent;
use Busybee\SecurityBundle\BusybeeSecurityEvents;
use Busybee\SecurityBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse ;
use Busybee\SecurityBundle\Validator\Password ;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

class UserController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted ;

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
     * @param Request $request
     * @param string $token
     * @return null|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function resetAction(Request $request, $token)
    {

        /** @var $userManager \Busybee\SecurityBundle\Model\UserManagerInterface */
        $userManager = $this->get('busybee_security.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);


        if (null === $user) {
            throw new TokenNotFoundException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
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
        $config = new \stdClass();
        $config->misc = new \stdClass();
        $config->misc->password = $this->get('system.password.manager')->buildPassword($this->getParameter('password'));

        return $this->render('BusybeeSecurityBundle:User:reset.html.twig', array(
            'token' => $token,
            'config' => $config,
            'form' => $form->createView(),
        ));
    }

    /**
     * Request reset user password: show form
     * @param integer $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
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

        $comment = $request->get('comment');

        $this->get('busybee_security.mailer')->sendResettingEmailMessage($user, $comment);
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
     * @param UserInterface $user
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
     * @param integer $personID
     * @return RedirectResponse
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
            )
        )
        );

    }

    /**
     * Tell the user to check his email provider
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
     * toggle User Enabled
     * @param integer $id
     * @return JsonResponse
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
