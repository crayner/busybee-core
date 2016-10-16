<?php

namespace Busybee\SystemBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use stdClass ;
use Symfony\Component\Yaml\Yaml ;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\RedirectResponse ;


class InstallController extends Controller
{
    public function indexAction()
    {
		$config = new stdClass();
		$config->signin = null;
		
		$config->parameterStatus = is_writable($this->get('kernel')->getRootDir().'/config/parameters.yml');
		
		$params = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir().'/config/parameters.yml'));
		$params = $params['parameters'];
		$config->sql = new stdClass();
		$sql = array();
		foreach($params as $name=>$value)
			if (strpos($name, 'database_') === 0)
				{
					$config->sql->$name = $value;
					$sql[substr($name, 9)] = $value;
				}
		$connectionFactory = $this->get('doctrine.dbal.connection_factory');
		$connection = $connectionFactory->createConnection($sql);
		try {
			$connection->executeQuery('SHOW TABLES');	
		} catch (\Exception $e)
		{
			$config->sql->error = $e->getMessage();
			//do nothing ...
		}
		$config->sql->isConnected = $connection->isConnected();
		
        return $this->render('SystemBundle:Install:start.html.twig', array('config' => $config));
    }
	
	public function saveDatabaseAction(Request $request)
	{
		$csrf = $this->get('security.csrf.token_manager');
		if (! $csrf->isTokenValid(new CsrfToken('database', $request->request->get('_csrf_token')))) die();
		$params = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir().'/config/parameters.yml'));
		foreach($params['parameters'] as $name => $value)
			if (strpos($name, 'database_') === 0)
				{
					$postName = substr($name, 9);
					$params['parameters'][$name] = $request->request->get($postName);
				}
		if (! file_put_contents($this->get('kernel')->getRootDir().'/config/parameters.yml', Yaml::dump($params)))
			return new RedirectResponse($this->generateUrl('error_page', array('message' => 'error.save.parameters')));
		else
		{
			return new RedirectResponse($this->generateUrl('install_start'));
		}
	}
}
