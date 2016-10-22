<?php

namespace Busybee\SystemBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use stdClass ;
use Doctrine\ORM\Tools\SchemaTool ;
use Symfony\Component\HttpFoundation\JsonResponse ;

class UpdateController extends Controller
{
    public function indexAction()
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_ADMIN'))) return $response;
		$config = new stdClass();
		$config->signin = null;
		
        $em = $this->get('doctrine')->getManager();

        $schemaTool = new SchemaTool($em);
        $metaData = $em->getMetadataFactory()->getAllMetadata();

		$xx = $schemaTool->getUpdateSchemaSql($metaData, false);
		
		$config->version = $this->getParameter('version');

		$setting = $this->get('system.setting.manager');	
		
		$config->Versiondb = $setting->getSetting('Version.Database', '0.0.00');
		$config->VersionSys = $setting->getSetting('Version.System', '0.0.00');
		$count = 0;

		$sysVersion = $config->VersionSys;
		while (version_compare($sysVersion, $config->version ['system'], '<')) {
			$v = 'update_'.str_replace('.', '_', $sysVersion);
			if (method_exists ( $this , $v ))
				$count++;
			$sysVersion = $this->incrementVersion($sysVersion);
		}
		
		$config->dbUpdate = count($xx) + $count;

        return $this->render('SystemBundle:Update:index.html.twig', array('config' => $config));
    }

    public function databaseAction()
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_ADMIN'))) return $response;

		$config = new stdClass();
		$config->signin = null;
		
        $em = $this->get('doctrine')->getManager();

        $schemaTool = new SchemaTool($em);
        $metaData = $em->getMetadataFactory()->getAllMetadata();

		$xx = $schemaTool->updateSchema($metaData, true);

		$config->version = $version = $this->getParameter('version');

		$setting = $this->get('system.setting.manager');	

		$config->Versiondb = $setting->getSetting('Version.Database', '0.0.00');
		$VersionSys = $config->VersionSys = $setting->getSetting('Version.System', '0.0.00');
		
		 while (version_compare($VersionSys, $version['system'], '<')) {
			$v = 'update_'.str_replace('.', '_', $VersionSys);
			if (method_exists ( $this , $v ))
				$this->$v();
			$VersionSys = $this->incrementVersion($VersionSys);
		}
 
 		$sm = $this->get('system.setting.manager');

		$sm	->setSetting('Version.Database', $version['database'])
			->setSetting('Version.System', $version['system']);
 
		$config->dbUpdate = 0;
		$config->Versiondb = $setting->getSetting('Version.Database', '0.0.00');
		$config->VersionSys = $setting->getSetting('Version.System', '0.0.00');
		
		return new JsonResponse(
			array(
				'content' => $this->renderView('SystemBundle:Update:index_content.html.twig', array('config' => $config)),
			200)
		);

    }

	/**
	 * increment Version
	 *
	 * @version	20th October 2016
	 * @since	20th October 2016
	 * @param	string	$version
	 * @return	string Version
	 */
    private function incrementVersion($version)
    {
		$v = explode('.', $version);
		if (!isset($v[2])) $v[2] = 0;
		if (!isset($v[1])) $v[1] = 0;
		if (!isset($v[0])) $v[0] = 0;
		while (count($v) > 3)
			array_pop($v);
		$v[2]++;
		if ($v[2] > 99) 
		{
			$v[2] = 0;
			$v[1]++;
		}
		if ($v[1] > 9)
		{
			$v[1] = 0;
			$v[0]++;
		}
		$v[2] = str_pad($v[2], 2, '00', STR_PAD_LEFT);
		return implode('.', $v);
	}

	/**
	 * increment Version
	 *
	 * @version	21st October 2016
	 * @since	20th October 2016
	 * @param	string	$version
	 * @return	string Version
	 */
    private function update_0_0_00()
    {
		$em = $this->get('doctrine')->getManager();
		
		$sm = $this->get('system.setting.manager');
		$role = $em->getRepository('BusybeeSecurityBundle:Role');
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('string');
		$entity->setValue('0.0.00');
		$entity->setName('Version.System');
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));

		$sm->saveSetting($entity);
		
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('string');
		$entity->setValue('0.0.00');
		$entity->setName('Version.Database');
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));

		$sm->saveSetting($entity);
		
		
	}
}