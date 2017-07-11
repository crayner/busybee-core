<?php

namespace Busybee\SystemBundle\Controller ;

use Busybee\InstituteBundle\Entity\Term;
use Busybee\InstituteBundle\Entity\Year;
use Busybee\PersonBundle\Entity\Person;
use Busybee\StaffBundle\Entity\Staff;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\SyntaxErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\Yaml\Yaml ;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\Security\Csrf\CsrfToken ;
use Symfony\Component\HttpFoundation\RedirectResponse ;
use Doctrine\ORM\EntityManager ;
use Doctrine\ORM\Tools\SchemaTool ;
use Busybee\SecurityBundle\Entity\User ;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class InstallController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted ;

    public function indexAction()
    {
        $config = new \stdClass();
        $config->signin = null;

        $config->parameterStatus = is_writable($this->get('kernel')->getRootDir() . '/config/parameters.yml');

        $params = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir() . '/config/parameters.yml'));
        $params = $params['parameters'];
        $config->sql = new \stdClass();
        $sql = [];

        foreach ($params as $name => $value)
            if (strpos($name, 'database_') === 0) {
                $config->sql->$name = $value;
                $sql[substr($name, 9)] = $value;
            }
        $session = $this->get('session');

        unset($sql['name']);
        if (!$session->isStarted() || !$session->has('databaseException')) {
            $caught = false;
            $config->sql->error = 'No Error Detected.';
            $connectionFactory = $this->get('doctrine.dbal.connection_factory');
            $connection = $connectionFactory->createConnection($sql);
            try {
                $connection->connect();
            } catch (ConnectionException | \Exception $e) {
                $config->sql->error = $e->getMessage();
                $config->sql->isConnected = false;
                $config->exception = $e;
                $caught = true;
                return $this->render('SystemBundle:Install:start.html.twig', array('config' => $config));
            }
            $config->sql->isConnected = $connection->isConnected();

            if (!$caught) {
                $dbExists = true;
                try {
                    $connection->executeQuery("CREATE DATABASE IF NOT EXISTS " . $config->sql->database_name);
                } catch (SyntaxErrorException $e) {
                    $config->sql->error = $e->getMessage() . '. <strong>The database name must not have any spaces.</strong>';
                    $config->sql->isConnected = false;
                    $config->exception = $e;
                    $dbExists = false;
                    return $this->render('SystemBundle:Install:start.html.twig', array('config' => $config));
                }
                if ($dbExists) {
                    $sql['name'] = $config->sql->database_name;
                    $connection = $connectionFactory->createConnection($sql);
                    try {
                        $connection->connect();
                    } catch (ConnectionException | SyntaxErrorException | \Exception $e) {
                        $config->sql->error = $e->getMessage();
                        $config->sql->isConnected = false;
                        $config->exception = $e;
                        $caught = true;
                        return $this->render('SystemBundle:Install:start.html.twig', array('config' => $config));
                    }
                    $config->sql->isConnected = $connection->isConnected();
                }
            }
        } else {
            $config->exception = $this->get('session')->get('databaseException');
            $config->sql->isConnected = false;
            $config->sql->error = $config->exception->getMessage();
        }

        return $this->render('SystemBundle:Install:start.html.twig', array('config' => $config));
    }

    public function saveDatabaseAction(Request $request)
    {
        if (!$this->isCsrfTokenValid('database', $request->request->get('_csrf_token'))) die();
        $params = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir().'/config/parameters.yml'));
        foreach($params['parameters'] as $name => $value)
            if (strpos($name, 'database_') === 0) {
                $postName = substr($name, 9);
                $params['parameters'][$name] = $request->request->get($postName);
            }
        if (! file_put_contents($this->get('kernel')->getRootDir().'/config/parameters.yml', Yaml::dump($params)))
            return new RedirectResponse($this->generateUrl('error_page', array('message' => 'error.save.parameters')));
        else {
            $this->get('session')->getFlashBag()->set('success', 'success.save.parameters');
            return new RedirectResponse($this->generateUrl('install_start'));
        }
    }

    public function checkMailerAction(Request $request)
    {

        $config = new \stdClass();
        $config->signin = null;

        $w = is_writable($this->get('kernel')->getRootDir().'/config/parameters.yml');

        // Turn off the spooler
        $w = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir().'/config/config.yml'));
        $swift = $w['swiftmailer'];
        $swift['transport'] = "%mailer_transport%";
        $swift['host'] = "%mailer_host%";
        $swift['username'] = "%mailer_user%";
        $swift['password'] = "%mailer_password%";
        $w['swiftmailer'] = $swift ;
        file_put_contents($this->get('kernel')->getRootDir().'/config/config.yml', Yaml::dump($w));

        $params = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir().'/config/parameters.yml'));
        $params = $params['parameters'];
        $config->mailer = new \stdClass();
        $sql = array();
        foreach($params as $name=> $value)
            if (strpos($name, 'mailer_') === 0) {
                $config->mailer->$name = $value;
                $sql[substr($name, 7)] = $value;
            }

        $config->mailer->canDeliver = false;
        if ($config->mailer->mailer_transport != '') {
            $message = \Swift_Message::newInstance()
                ->setSubject('Test Email')
                ->setFrom($config->mailer->mailer_sender_address, $config->mailer->mailer_sender_name)
                ->setTo($config->mailer->mailer_sender_address, $config->mailer->mailer_sender_name)
                ->setBody(
                    $this->renderView(
                        'SystemBundle:Emails:test.html.twig',
                        array('name' => $name)
                    ),
                    'text/html'
                )/*
					 * If you also want to include a plaintext version of the message
					->addPart(
						$this->renderView(
							'Emails/registration.txt.twig',
							array('name' => $name)
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

        return $this->render('SystemBundle:Install:checkMailer.html.twig', array('config' => $config));
    }

    public function saveMailerAction(Request $request)
    {
        if (!$this->isCsrfTokenValid('mailer', $request->request->get('_csrf_token'))) die();

        $params = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir().'/config/parameters.yml'));
        foreach($params['parameters'] as $name => $value)
            if (strpos($name, 'mailer_') === 0) {
                $postName = substr($name, 7);
                $params['parameters'][$name] = $request->request->get($postName);
            }
        if ($request->request->get('transport') == 'gmail')
            $params['parameters']['mailer_host'] = 'smtp.gmail.com';
        elseif ($request->request->get('transport') != 'smtp') {
            $params['parameters']['mailer_host'] = null;
            $params['parameters']['mailer_port'] = null;
            $params['parameters']['mailer_encryption'] = null;
            $params['parameters']['mailer_auth_mode'] = null;
        } elseif ($request->request->get('transport') == '') {
            $params['parameters']['mailer_user'] = null;
            $params['parameters']['mailer_password'] = null;
            $params['parameters']['mailer_sender_name'] = null;
            $params['parameters']['mailer_sender_address'] = null;
        }

        if (! file_put_contents($this->get('kernel')->getRootDir().'/config/parameters.yml', Yaml::dump($params)))
            return new RedirectResponse($this->generateUrl('error_page', array('message' => 'error.save.parameters')));
        else {
            $this->get('session')->getFlashBag()->set('success', 'success.save.parameters');
            return new RedirectResponse($this->generateUrl('install_check_mailer_parameters'));
        }
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