<?php

namespace Busybee\SystemBundle\Controller ;

use Busybee\InstituteBundle\Entity\Term;
use Busybee\InstituteBundle\Entity\Year;
use Busybee\PersonBundle\Entity\Person;
use Busybee\StaffBundle\Entity\Staff;
use Busybee\SystemBundle\Form\MailerType;
use Busybee\SystemBundle\Form\StartInstallType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\Yaml\Yaml ;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\HttpFoundation\RedirectResponse ;
use Doctrine\ORM\EntityManager ;
use Doctrine\ORM\Tools\SchemaTool ;
use Busybee\SecurityBundle\Entity\User ;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class InstallController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted ;

    public function indexAction(Request $request)
    {
        $config = $this->get('install.manager');
        $config->signin = null;

        $params = $config->getParameters();
        $sql = $config->getSQLParameters($params);

        $form = $this->createForm(StartInstallType::class, null, ['data' => $sql]);

        $sql = $config->handleDataBaseRequest($form, $request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$config->testConnected($sql)) {
                return $this->render('SystemBundle:Install:start.html.twig',
                    [
                        'config' => $config,
                        'form' => $form->createView(),
                    ]
                );

            }

            if (!$config->hasDatabase($sql)) {
                return $this->render('SystemBundle:Install:start.html.twig',
                    [
                        'config' => $config,
                        'form' => $form->createView(),
                    ]
                );

            }

            if ($config->saveDatabase)
                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('install.database.save.success', [], 'SystemBundle'));
            else {
                $this->get('session')->getFlashBag()->add('danger', $this->get('translator')->trans('install.database.save.failed', [], 'SystemBundle'));
                $config->sql->connected = false;
                $config->sql->error = $this->get('translator')->trans('install.database.save.failed', [], 'SystemBundle');
            }
        } else {
            $config->sql->connected = false;
            $config->sql->error = $this->get('translator')->trans('install.database.not.tested', [], 'SystemBundle');
        }

        return $this->render('SystemBundle:Install:start.html.twig',
            [
                'config' => $config,
                'form' => $form->createView(),
            ]
        );
    }

    public function checkMailerAction(Request $request)
    {

        $config = $this->get('install.manager');

        $w = $config->getConfig();


        $swift = $w['swiftmailer'];
        $swift['transport'] = "%mailer_transport%";
        $swift['host'] = "%mailer_host%";
        $swift['username'] = "%mailer_user%";
        $swift['password'] = "%mailer_password%";
        $w['swiftmailer'] = $swift ;
        $config->saveConfig($w);

        $config->getMailerParameters();
        $config->mailer->canDeliver = false;

        $form = $this->createForm(MailerType::class);

        $config->handleMailerRequest($form, $request);

        if ($form->isSubmitted() && $form->isValid()) {
            $config->mailer->canDeliver = false;
            if ($config->mailer->mailer_transport != '') {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Test Email')
                    ->setFrom($config->mailer->mailer_sender_address, $config->mailer->mailer_sender_name)
                    ->setTo($config->mailer->mailer_sender_address, $config->mailer->mailer_sender_name)
                    ->setBody(
                        $this->renderView(
                            'SystemBundle:Emails:test.html.twig', []
                        ),
                        'text/html'
                    )/*
					 * If you also want to include a plaintext version of the message
					->addPart(
						$this->renderView(
							'Emails/registration.txt.twig', []
						),
						'text/plain'
					)
					*/
                ;
                $config->mailer->canDeliver = true;
                try {
                    $mailer = $this->get('mailer')->send($message);
                } catch (\Swift_TransportException $e) {
                    $this->get('session')->getFlashBag()->add('error', $e->getMessage());
                    $config->mailer->canDeliver = false;
                } catch (\Swift_RfcComplianceException $e) {
                    $this->get('session')->getFlashBag()->add('error', $e->getMessage());
                    $config->mailer->canDeliver = false;
                }
            }
        }

        if ($config->saveMailer)
            $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('mailer.save.success', [], 'SystemBundle'));
        else {
            $this->get('session')->getFlashBag()->add('danger', $this->get('translator')->trans('mailer.save.failed', [], 'SystemBundle'));
            $config->mailer->canDeliver = false;
        }
        return $this->render('SystemBundle:Install:checkMailer.html.twig',
            [
                'config' => $config,
                'form' => $form->createView(),
            ]
        );
    }

    public function miscCheckAction(Request $request)
    {

        $w = is_writable($this->get('kernel')->getRootDir().'/config/config.yml');
        $config = new \stdClass();
        $config->signin = null;

        // Turn off the spooler
        $w = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir().'/config/config.yml'));
        $swift = $w['swiftmailer'];
        $swift['transport'] = "%mailer_transport%";
        $swift['host'] = "%mailer_host%";
        $swift['username'] = "%mailer_user%";
        $swift['password'] = "%mailer_password%";
        $swift['spool']['type'] =  'memory';
        $w['swiftmailer'] = $swift ;
        file_put_contents($this->get('kernel')->getRootDir().'/config/config.yml', Yaml::dump($w));

        $params = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir().'/config/parameters.yml'));
        $config->misc = new \stdClass();
        $config->proceed = true;
        if (! empty($params['parameters']['user'])) {
            $config->misc->username = $params['parameters']['user']['name'];
            $config->misc->email = $params['parameters']['user']['email'];
            $config->misc->password1 = $params['parameters']['user']['password'];
            $config->misc->password2 = $params['parameters']['user']['password'];
            $config->proceed = $params['parameters']['user']['valid'];

        } else {
            $config->misc->username = null;
            $config->misc->email = null;
            $config->misc->password1 = null;
            $config->misc->password2 = null;
            $config->proceed = false;
        }

        $config->misc->password = $this->get('system.password.manager')->buildPassword($this->getParameter('password'));


        $valueList = array(
            'secret' => '',
            'locale' => '',
            'session_name' => '',
            'session_remember_me_name' => '',
            'session_max_idle_time' => '',
            'country' => '',
            'signin_count_minimum' => '',
        );

        foreach($valueList as $name => $value)
            $config->misc->$name = $params['parameters'][$name];
        if ($params['parameters']['secret'] == 'ThisTokenIsNotSoSecretChangeIt') {
            $config->proceed = false ;
            $config->misc->secret = md5(uniqid());
        }
        if (empty($params['parameters']['locale']))
            $config->proceed = false ;
        if (empty($params['parameters']['session_name']))
            $config->proceed = false ;
        else {
            $config->misc->session_remember_me_name = $config->misc->session_name . '_remember';
        }
        $config->misc->session_max_idle_time = $config->misc->session_max_idle_time < 300 ? 900 : $config->misc->session_max_idle_time;
        $config->misc->country = empty($params['parameters']['country']) ? null : $params['parameters']['country'] ;

        if (empty($config->misc->country))
            $config->proceed = false ;
        if ($config->misc->signin_count_minimum < 3 || $config->misc->signin_count_minimum > 10)
            $config->misc->signin_count_minimum = 3 ;
        $config->countryList = Intl::getRegionBundle()->getCountryNames();
//die();
        return $this->render('SystemBundle:Install:misc.html.twig', array('config' => $config));
    }

    public function miscSaveAction(Request $request)
    {
        if (!$this->isCsrfTokenValid('miscellaneous', $request->request->get('_csrf_token'))) die();

        $params = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir().'/config/parameters.yml'));

        $params['parameters']["secret"] = $request->request->get('secret');
        $params['parameters']["session_name"] = $request->request->get('session_name');
        $params['parameters']["session_remember_me_name"] = $request->request->get('session_name') . '_remember';
        $params['parameters']["session_max_idle_time"] = $request->request->get('session_max_idle_time');
        $params['parameters']["signin_count_minimum"] = $request->request->get('signin_count_minimum');
        $params['parameters']["locale"] = $request->request->get('locale');
        $params['parameters']["country"] = $request->request->get('country');

        $params['parameters']["user"]['email'] = $request->request->get('email') ;
        $valid = true ;
        $params['parameters']["user"]['name'] = empty($request->request->get('username')) ? $request->request->get('email') : $request->request->get('username') ;

        if (empty($request->request->get('password1')) || $request->request->get('password1') !== $request->request->get('password2')) {
            $this->get('session')->getFlashBag()->add('error', 'error.password.notMatch');
            $valid = false;
        }

        $params['parameters']['password']['mixedCase'] = $request->request->get('mixedCase') == 'on' ? true : false ;
        $params['parameters']['password']['numbers'] = $request->request->get('numbers') == 'on' ? true : false ;
        $params['parameters']['password']['specials'] = $request->request->get('specials') == 'on' ? true : false ;
        $params['parameters']['password']['minLength'] = $request->request->get('minLength') >= 6 && $request->request->get('minLength') <= 25 ? intval($request->request->get('minLength')) : 8 ;

        $pattern = "^(.*";
        if ( $params['parameters']['password']['mixedCase']) {
            $pattern .= "(?=.*[a-z])(?=.*[A-Z])";
        }
        if ($params['parameters']['password']['numbers']) {
            $pattern .= "(?=.*[0-9])";
        }
        if ($params['parameters']['password']['specials']) {
            $pattern .= "(?=.*?[#?!@$%^&*-])";
        }
        $pattern .= ".*){".$params['parameters']['password']['minLength'].",}$";
        if (preg_match('/'.$pattern.'/', $request->request->get('password1')) !== 1) {
            $this->get('session')->getFlashBag()->add('error', 'error.password.notValid');
            $valid = false;
        }

        $params['parameters']["user"]['password'] = $request->request->get('password1');

        $validator = $this->get('validator');

        $constraints = array(
            new \Symfony\Component\Validator\Constraints\Email(),
            new \Symfony\Component\Validator\Constraints\NotBlank()
        );


        $errors = $validator->validate($params['parameters']["user"]['email'], $constraints);

        if (count($errors) > 0) {
            $this->get('session')->getFlashBag()->add('error', $errors->get(0)->getMessage());
            $valid = false;
        }
        $params['parameters']["user"]['valid'] = $valid;


        if (! file_put_contents($this->get('kernel')->getRootDir().'/config/parameters.yml', Yaml::dump($params)))
            return new RedirectResponse($this->generateUrl('error_page', array('message' => 'error.save.parameters')));
        else {
            if ($valid) {
                $session = $this->get('session');
                $session->getFlashBag()->set('success', 'success.save.parameters');
            }
            return new RedirectResponse($this->generateUrl('install_misc_check'));
        }
    }

    public function buildAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager('default');
        $x = $this->get('doctrine')->getManager('default');
        $newEm = EntityManager::create($x->getConnection(), $em->getConfiguration());
        $meta = $em->getMetadataFactory()->getAllMetadata();

        $tool = new SchemaTool($newEm);
        $tool->createSchema($meta);


        $this->entity = $newEm->getRepository('BusybeeSecurityBundle:User')->find(1);
        if (is_null($this->entity))
            $this->entity = new User();
        $parameters = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir() . '/config/parameters.yml'));
        $user = $parameters['parameters']['user'];

        if (intval($this->entity->getId()) == 0) {
            $this->entity->setUsername($user['name']);
            $this->entity->setUsernameCanonical($user['name']);
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
            $encoder = $this->get('security.password_encoder');
            $password = $encoder->encodePassword($this->entity, $user['password']);
            $this->entity->setPassword($password);
            $newEm->persist($this->entity);
            $newEm->flush();
        }

        $user = $newEm->getRepository(User::class)->find(1);

        $session = $this->get('session');
        // Here, "default" is the name of the firewall in your security.yml
        $token = new UsernamePasswordToken($user, null, "default", $user->getRoles());

        $this->get('security.token_storage')->setToken($token);

        unset($parameters['parameters']['user']);

        file_put_contents($this->get('kernel')->getRootDir() . '/config/parameters.yml', Yaml::dump($parameters));

        $this->get('session')->getFlashBag()->add('success', 'buildDatabase.success');

        if (!$user->hasPerson()) {
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

        if (empty($year->getId())) {
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

        return new RedirectResponse($this->generateUrl('update_start'));
    }

    public function connectionFailAction($exception)
    {
        $config = new \stdClass();
        $config->signin = null;

        $config->parameterStatus = is_writable($this->get('kernel')->getRootDir() . '/config/parameters.yml');

        $params = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir() . '/config/parameters.yml'));
        $params = $params['parameters'];

        $config->exception = $exception;

        return $this->render('@System/Install/connectionfail.html.twig', [
            'config' => $config,
        ]);
    }
}