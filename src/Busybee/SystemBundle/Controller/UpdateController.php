<?php

namespace Busybee\SystemBundle\Controller ;

use Busybee\SystemBundle\Entity\Setting;
use Symfony\Bundle\FrameworkBundle\Controller\Controller ;

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
        if (! $sm->get('Installed')) {
            $entity = new Setting();
            $entity->setType('boolean');
            $entity->setValue(true);
            $entity->setName('Installed');
            $entity->setDisplayName('System Installed');
            $entity->setDescription('A flag showing the system has finished installing.');
            $entity->setRole('ROLE_SUPER_ADMIN');

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

}