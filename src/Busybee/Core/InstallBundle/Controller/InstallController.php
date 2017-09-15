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
	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction(Request $request)
	{
		$installer         = $this->get('busybee_core_template.model.install_manager');
		$installer->signin = null;

		$params = $installer->getParameters();
		$sql    = $installer->getSQLParameters($params);

		$form = $this->createForm(StartInstallType::class, null, ['data' => $sql]);

		$sql = $installer->handleDataBaseRequest($form, $request);

		$testDatabase = false;
		if (! empty($sql['name']) && ! empty($sql['user']) && ! empty($sql['password']))
			$testDatabase = true;

		if (($form->isSubmitted() && $form->isValid()) || $testDatabase)
		{

			if (!$installer->testConnected($sql))
			{
				return $this->render('BusybeeInstallBundle:Install:start.html.twig',
					[
						'config' => $installer,
						'form'   => $form->createView(),
					]
				);

			}

			if (!$installer->hasDatabase())
			{
				return $this->render('BusybeeInstallBundle:Install:start.html.twig',
					[
						'config' => $installer,
						'form'   => $form->createView(),
					]
				);

			}
			$installer->sql->displayForm = false;

			if ($installer->saveDatabase)
			{
				$this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('install.database.save.success', [], 'BusybeeInstallBundle'));
			}
			elseif($installer->sql->connected)
			{
				$this->get('session')->getFlashBag()->add('info', $this->get('translator')->trans('install.database.save.already', [], 'BusybeeInstallBundle'));
				$installer->sql->displayForm = true;
			}
			else
			{
				$this->get('session')->getFlashBag()->add('danger', $this->get('translator')->trans('install.database.save.failed', [], 'BusybeeInstallBundle'));
				$installer->sql->connected = false;
				$installer->sql->error     = $this->get('translator')->trans('install.database.save.failed', [], 'BusybeeInstallBundle');
			}
		}
		else
		{
			$installer->sql->connected = false;
			$installer->sql->error     = $this->get('translator')->trans('install.database.not.tested', [], 'BusybeeInstallBundle');
		}

		return $this->render('BusybeeInstallBundle:Install:start.html.twig',
			[
				'config'          => $installer,
				'form'            => $form->createView(),
				'version_manager' => $this->get('busybee_core_template.model.version_manager'),
			]
		);
	}

	public function mailerAction(Request $request)
	{

		$installer = $this->get('busybee_core_template.model.install_manager');

		$w = $installer->getConfig();

		//turn spooler off
		$swift              = $w['swiftmailer'];
		$swift['transport'] = "%mailer_transport%";
		$swift['host']      = "%mailer_host%";
		$swift['username']  = "%mailer_user%";
		$swift['password']  = "%mailer_password%";
		$w['swiftmailer']   = $swift;
		$installer->saveConfig($w);

		$installer->getMailerParameters();
		$installer->mailer->canDeliver = false;

		$form = $this->createForm(MailerType::class);

		$installer->handleMailerRequest($form, $request);

		$installer->mailer->canDeliver = false;
		if ($installer->mailer->mailer_transport != '')
		{
			$message                    = \Swift_Message::newInstance()
				->setSubject('Test Email')
				->setFrom($installer->mailer->mailer_sender_address, $installer->mailer->mailer_sender_name)
				->setTo($installer->mailer->mailer_sender_address, $installer->mailer->mailer_sender_name)
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
			$installer->mailer->canDeliver = true;
			try
			{
				$mailer = $this->get('mailer')->send($message);
			}
			catch (\Swift_TransportException $e)
			{
				$this->get('session')->getFlashBag()->add('error', $e->getMessage());
				$installer->mailer->canDeliver = false;
			}
			catch (\Swift_RfcComplianceException $e)
			{
				$this->get('session')->getFlashBag()->add('error', $e->getMessage());
				$installer->mailer->canDeliver = false;
			}
		}

		if ($form->isSubmitted())
		{
			if ($installer->saveMailer)
				$this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('mailer.save.success', [], 'BusybeeInstallBundle'));
			else
			{
				$this->get('session')->getFlashBag()->add('danger', $this->get('translator')->trans('mailer.save.failed', [], 'BusybeeInstallBundle'));
				$installer->mailer->canDeliver = false;
			}
		} elseif ($installer->mailer->canDeliver)
		{
			$this->get('session')->getFlashBag()->add('info', $this->get('translator')->trans('mailer.save.already', [], 'BusybeeInstallBundle'));
		}

		return $this->render('BusybeeInstallBundle:Install:checkMailer.html.twig',
			[
				'config' => $installer,
				'form'   => $form->createView(),
			]
		);
	}

	public function miscellaneousAction(Request $request)
	{

		$installer          = $this->get('busybee_core_template.model.install_manager');
		$installer->proceed = false;

		$w = $installer->getConfig();

		//turn spooler on
		$swift                  = $w['swiftmailer'];
		$swift['transport']     = "%mailer_transport%";
		$swift['host']          = "%mailer_host%";
		$swift['username']      = "%mailer_user%";
		$swift['password']      = "%mailer_password%";
		$swift['spool']['type'] = 'memory';
		$w['swiftmailer']       = $swift;
		$installer->saveConfig($w);

		$form = $this->createForm(MiscellaneousType::class);

		$installer->handleMiscellaneousRequest($form, $request);

		return $this->render('BusybeeInstallBundle:Install:misc.html.twig',
			[
				'config' => $installer,
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