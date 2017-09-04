<?php

namespace Busybee\Core\InstallBundle\Controller;

use Busybee\Core\CalendarBundle\Entity\Term;
use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\People\PersonBundle\Entity\Person;
use Busybee\People\StaffBundle\Entity\Staff;
use Busybee\Core\InstallBundle\Form\MailerType;
use Busybee\Core\InstallBundle\Form\MiscellaneousType;
use Busybee\Core\InstallBundle\Form\StartInstallType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Busybee\Core\SecurityBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Yaml\Yaml;

class InstallController extends Controller
{
	public function indexAction(Request $request)
	{
		$config         = $this->get('install.manager');
		$config->signin = null;

		$params = $config->getParameters();
		$sql    = $config->getSQLParameters($params);

		$form = $this->createForm(StartInstallType::class, null, ['data' => $sql]);

		$sql = $config->handleDataBaseRequest($form, $request);

		if ($form->isSubmitted() && $form->isValid())
		{

			if (!$config->testConnected($sql))
			{
				return $this->render('BusybeeInstallBundle:Install:start.html.twig',
					[
						'config' => $config,
						'form'   => $form->createView(),
					]
				);

			}

			if (!$config->hasDatabase())
			{
				return $this->render('BusybeeInstallBundle:Install:start.html.twig',
					[
						'config' => $config,
						'form'   => $form->createView(),
					]
				);

			}

			if ($config->saveDatabase)
			{
				$this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('install.database.save.success', [], 'BusybeeInstallBundle'));
			}
			else
			{
				$this->get('session')->getFlashBag()->add('danger', $this->get('translator')->trans('install.database.save.failed', [], 'BusybeeInstallBundle'));
				$config->sql->connected = false;
				$config->sql->error     = $this->get('translator')->trans('install.database.save.failed', [], 'BusybeeInstallBundle');
			}
		}
		else
		{
			$config->sql->connected = false;
			$config->sql->error     = $this->get('translator')->trans('install.database.not.tested', [], 'BusybeeInstallBundle');
		}

		return $this->render('BusybeeInstallBundle:Install:start.html.twig',
			[
				'config'          => $config,
				'form'            => $form->createView(),
				'version_manager' => $this->get('version.manager'),
			]
		);
	}

	public function mailerAction(Request $request)
	{

		$config = $this->get('install.manager');

		$w = $config->getConfig();

		//turn spooler off
		$swift              = $w['swiftmailer'];
		$swift['transport'] = "%mailer_transport%";
		$swift['host']      = "%mailer_host%";
		$swift['username']  = "%mailer_user%";
		$swift['password']  = "%mailer_password%";
		$w['swiftmailer']   = $swift;
		$config->saveConfig($w);

		$config->getMailerParameters();
		$config->mailer->canDeliver = false;

		$form = $this->createForm(MailerType::class);

		$config->handleMailerRequest($form, $request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$config->mailer->canDeliver = false;
			if ($config->mailer->mailer_transport != '')
			{
				$message                    = \Swift_Message::newInstance()
					->setSubject('Test Email')
					->setFrom($config->mailer->mailer_sender_address, $config->mailer->mailer_sender_name)
					->setTo($config->mailer->mailer_sender_address, $config->mailer->mailer_sender_name)
					->setBody(
						$this->renderView(
							'BusybeeInstallBundle:Emails:test.html.twig', []
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
				try
				{
					$mailer = $this->get('mailer')->send($message);
				}
				catch (\Swift_TransportException $e)
				{
					$this->get('session')->getFlashBag()->add('error', $e->getMessage());
					$config->mailer->canDeliver = false;
				}
				catch (\Swift_RfcComplianceException $e)
				{
					$this->get('session')->getFlashBag()->add('error', $e->getMessage());
					$config->mailer->canDeliver = false;
				}
			}
		}

		if ($form->isSubmitted())
		{
			if ($config->saveMailer)
				$this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('mailer.save.success', [], 'BusybeeInstallBundle'));
			else
			{
				$this->get('session')->getFlashBag()->add('danger', $this->get('translator')->trans('mailer.save.failed', [], 'BusybeeInstallBundle'));
				$config->mailer->canDeliver = false;
			}
		}

		return $this->render('BusybeeInstallBundle:Install:checkMailer.html.twig',
			[
				'config' => $config,
				'form'   => $form->createView(),
			]
		);
	}

	public function miscellaneousAction(Request $request)
	{

		$config          = $this->get('install.manager');
		$config->proceed = false;

		$w = $config->getConfig();

		//turn spooler on
		$swift                  = $w['swiftmailer'];
		$swift['transport']     = "%mailer_transport%";
		$swift['host']          = "%mailer_host%";
		$swift['username']      = "%mailer_user%";
		$swift['password']      = "%mailer_password%";
		$swift['spool']['type'] = 'memory';
		$w['swiftmailer']       = $swift;
		$config->saveConfig($w);

		$form = $this->createForm(MiscellaneousType::class);

		$config->handleMiscellaneousRequest($form, $request);

		return $this->render('BusybeeInstallBundle:Install:misc.html.twig',
			[
				'config' => $config,
				'form'   => $form->createView(),
			]
		);
	}


	public function bundlesAction()
	{
		$bundles = $this->getParameter('bundles');

		foreach ($bundles as $name => $bundle)
		{
			if ($bundle['type'] === 'core')
				$bundle['active'] = true;
			else
				$bundle['active'] = false;
			$bundles[$name] = $bundle;
		}

		$path = $this->getParameter('kernel.project_dir') . '/app/config/bundles.yml';

		file_put_contents($path, Yaml::dump($bundles));

		$url = $this->generateUrl('install_build_system');

		$fs = new Filesystem();
		$fs->remove($this->getParameter('kernel.cache_dir'));

		return new RedirectResponse($url);
	}
}