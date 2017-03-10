<?php

namespace Busybee\SystemBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\JsonResponse ;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UpdateController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted ;

    public function indexAction()
    {
        $this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN');

        $config = new \stdClass();
		$config->signin = null;
		
		$um = $this->get('system.update.manager');
		
		$config->version = $um->getVersion();
		$config->dbUpdate = $um->getUpdateDetails();
		
		$sm = $this->get('setting.manager');
		if (! $sm->get('Installed'))
		{
			$role = $this->get('security.role.repository');

			$entity = new \Busybee\SystemBundle\Entity\Setting();
			$entity->setType('boolean');
			$entity->setValue(true);
			$entity->setName('Installed');
			$entity->setDisplayName('System Installed');
			$entity->setDescription('A flag showing the system has finished installing.');
			$entity->setRole($role->findOneByRole('ROLE_SUPER_ADMIN'));
	
			$sm->createSetting($entity);
		}

        return $this->render('SystemBundle:Update:index.html.twig', array('config' => $config));
    }

    public function databaseAction()
    {
        $this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN');

        $um = $this->get('system.update.manager');

        $um->build();

        $config = new \stdClass();
        $config->signin = null;

        $um = $this->get('system.update.manager');

        $config->version = $um->getVersion();
        $config->dbUpdate = $um->getUpdateDetails();

        return $this->render('SystemBundle:Update:index.html.twig', array('config' => $config));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function installAction()
    {
        $this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN');

        $config = new \stdClass();
        $config->signin = null;

        $sm = $this->get('setting.manager');
        if (!$sm->get('Installed')) {
            $role = $this->get('security.role.repository');

            $entity = new \Busybee\SystemBundle\Entity\Setting();
            $entity->setType('boolean');
            $entity->setValue(true);
            $entity->setName('Installed');
            $entity->setDisplayName('System Installed');
            $entity->setDescription('A flag showing the system has finished installing.');
            $entity->setRole($role->findOneByRole('ROLE_SUPER_ADMIN'));

            $sm->createSetting($entity);
        }

        $um = $this->get('system.update.manager');

        $um->build();

        $config->version = $um->getVersion();
        $config->dbUpdate = $um->getUpdateDetails();

        return $this->render('SystemBundle:Update:index.html.twig', array('config' => $config));

    }
}