<?php

namespace Busybee\SystemBundle\Controller ;

use Busybee\Core\CalendarBundle\Entity\Term;
use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\PersonBundle\Entity\Person;
use Busybee\StaffBundle\Entity\Staff;
use Busybee\SystemBundle\Event\MiscellaneousSubscriber;
use Busybee\SystemBundle\Form\MailerType;
use Busybee\SystemBundle\Form\MiscellaneousType;
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

            if (!$config->hasDatabase()) {
                return $this->render('SystemBundle:Install:start.html.twig',
                    [
                        'config' => $config,
                        'form' => $form->createView(),
                    ]
                );

            }

            if ($config->saveDatabase) {
                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('install.database.save.success', [], 'SystemBundle'));
            }
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
                'version_manager' => $this->get('version.manager'),
            ]
        );
    }

    public function mailerAction(Request $request)
    {

        $config = $this->get('install.manager');

        $w = $config->getConfig();

        //turn spooler off
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

        if ($form->isSubmitted()) {
            if ($config->saveMailer)
                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('mailer.save.success', [], 'SystemBundle'));
            else {
                $this->get('session')->getFlashBag()->add('danger', $this->get('translator')->trans('mailer.save.failed', [], 'SystemBundle'));
                $config->mailer->canDeliver = false;
            }
        }
        return $this->render('SystemBundle:Install:checkMailer.html.twig',
            [
                'config' => $config,
                'form' => $form->createView(),
            ]
        );
    }

    public function miscellaneousAction(Request $request)
    {

        $config = $this->get('install.manager');
        $config->proceed = false;

        $w = $config->getConfig();

        //turn spooler on
        $swift = $w['swiftmailer'];
        $swift['transport'] = "%mailer_transport%";
        $swift['host'] = "%mailer_host%";
        $swift['username'] = "%mailer_user%";
        $swift['password'] = "%mailer_password%";
        $swift['spool']['type'] = 'memory';
        $w['swiftmailer'] = $swift ;
        $config->saveConfig($w);

        $form = $this->createForm(MiscellaneousType::class);

        $config->handleMiscellaneousRequest($form, $request);

        return $this->render('SystemBundle:Install:misc.html.twig',
            [
                'config' => $config,
                'form' => $form->createView(),
            ]
        );
    }

    public function buildAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager('default');
        $x = $this->get('doctrine')->getManager('default');
        $newEm = EntityManager::create($x->getConnection(), $em->getConfiguration());
        $meta = $em->getMetadataFactory()->getAllMetadata();

        $tool = new SchemaTool($newEm);
        $tool->createSchema($meta);

        $im = $this->get('install.manager');

        $this->entity = $newEm->getRepository('BusybeeSecurityBundle:User')->find(1);
        if (is_null($this->entity))
            $this->entity = new User();
        $parameters = $im->getParameters();
        $user = $parameters['systemUser'];

        if (intval($this->entity->getId()) == 0) {
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
            $encoder = $this->get('security.password_encoder');
            $password = $encoder->encodePassword($this->entity, $user['password']);
            $this->entity->setPassword($password);
            $newEm->persist($this->entity);
            $newEm->flush();
        }


        $session = $this->get('session');

        $session->invalidate();

        unset($parameters['systemUser']);

        $im->saveParameters($parameters);

        $this->get('session')->getFlashBag()->add('success', 'buildDatabase.success');
        $user = $newEm->getRepository(User::class)->find(1);

        $token = new UsernamePasswordToken($user, null, "default", $user->getRoles());

        $this->get('security.token_storage')->setToken($token);

        return new RedirectResponse($this->generateUrl('install_build_complete'));
    }

    public function completeAction()
    {
        $this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN');

        $user = $this->get('user.repository')->find(1);

        $newEm = $this->get('doctrine')->getManager();

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

        $this->get('session')->getFlashBag()->add('success', 'buildComplete.success');

        return new RedirectResponse($this->generateUrl('update_database'));
    }
}