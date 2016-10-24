<?php

namespace Busybee\SystemBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use stdClass ;
use Symfony\Component\Yaml\Yaml ;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\Security\Csrf\CsrfToken ;
use Symfony\Component\HttpFoundation\RedirectResponse ;
use Doctrine\DBAL\DBALException ;
use Busybee\SystemBundle\EventListener\EmailListener ;
use Doctrine\ORM\EntityManager ;
use Doctrine\ORM\Tools\SchemaTool ;
use Busybee\SecurityBundle\Entity\User ;
use Busybee\SecurityBundle\Entity\Role ;
use Busybee\SecurityBundle\Entity\Group ;


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
		$caught = false;
		try {
			$connectionFactory = $this->get('doctrine.dbal.connection_factory');
			$connection = $connectionFactory->createConnection($sql);
			$w = $connection->executeQuery("CREATE DATABASE IF NOT EXISTS ".$config->sql->database_name);	
		} catch (\Exception $e)
		{
			$config->sql->error = $e->getMessage();
			$config->sql->isConnected = false ;
			$caught = true;
			//do nothing ...
		}
		catch (DBALException $e)
		{
			$config->sql->error = $e->getMessage();
			$config->sql->isConnected = false ;
			$caught = true;
			//do nothing ...
		}
		if (! $caught) $config->sql->isConnected = $connection->isConnected();
		
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
			$this->get('session')->getFlashBag()->set('success', 'success.save.parameters');
			return new RedirectResponse($this->generateUrl('install_start'));
		}
	}
	
	public function checkMailerAction(Request $request)
	{

		$config = new stdClass();
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
		$config->mailer = new stdClass();
		$sql = array();
		foreach($params as $name=>$value)
			if (strpos($name, 'mailer_') === 0)
				{
					$config->mailer->$name = $value;
					$sql[substr($name, 7)] = $value;
				}

		$config->mailer->canDeliver = false;
		if ($config->mailer->mailer_transport != '')
		{
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
					)
					/*
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
			} catch (\Swift_TransportException $e)
			{
				$this->get('session')->getFlashBag()->add('error', $e->getMessage());
				$config->mailer->canDeliver = false;
			} catch (\Swift_RfcComplianceException $e)
			{
				$this->get('session')->getFlashBag()->add('error', $e->getMessage());
				$config->mailer->canDeliver = false;
			}
		}

        return $this->render('SystemBundle:Install:checkMailer.html.twig', array('config' => $config));
	}
	
	public function saveMailerAction(Request $request)
	{
		$csrf = $this->get('security.csrf.token_manager');
		if (! $csrf->isTokenValid(new CsrfToken('mailer', $request->request->get('_csrf_token')))) die();

		$params = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir().'/config/parameters.yml'));
		foreach($params['parameters'] as $name => $value)
			if (strpos($name, 'mailer_') === 0)
				{
					$postName = substr($name, 7);
					$params['parameters'][$name] = $request->request->get($postName);
				}
		if ($request->request->get('transport') == 'gmail')
			$params['parameters']['mailer_host'] = 'smtp.gmail.com';
		elseif ($request->request->get('transport') != 'smtp')
		{
			$params['parameters']['mailer_host'] = null;
			$params['parameters']['mailer_port'] = null;
			$params['parameters']['mailer_encryption'] = null;
			$params['parameters']['mailer_auth_mode'] = null;
		}
		elseif ($request->request->get('transport') == '')
		{
			$params['parameters']['mailer_user'] = null;
			$params['parameters']['mailer_password'] = null;
			$params['parameters']['mailer_sender_name'] = null;
			$params['parameters']['mailer_sender_address'] = null;
		}
		
		if (! file_put_contents($this->get('kernel')->getRootDir().'/config/parameters.yml', Yaml::dump($params)))
			return new RedirectResponse($this->generateUrl('error_page', array('message' => 'error.save.parameters')));
		else
		{
			$this->get('session')->getFlashBag()->set('success', 'success.save.parameters');
			return new RedirectResponse($this->generateUrl('install_check_mailer_parameters'));
		}
	}
	
	public function miscCheckAction(Request $request)
	{
		
		$w = is_writable($this->get('kernel')->getRootDir().'/config/config.yml');
		$config = new stdClass();
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
		$config->misc = new stdClass();
		$config->proceed = true;
		if (! empty($params['parameters']['user']))
		{
			$config->misc->username = $params['parameters']['user']['name'];
			$config->misc->email = $params['parameters']['user']['email'];
			$config->misc->password1 = $params['parameters']['user']['password'];
			$config->misc->password2 = $params['parameters']['user']['password'];
			$config->proceed = $params['parameters']['user']['valid'];

		}
		else
		{
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
		if ($params['parameters']['secret'] == 'ThisTokenIsNotSoSecretChangeIt')
		{
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
		if (empty($config->misc->country))
			$config->proceed = false ;
		if ($config->misc->signin_count_minimum < 3 || $config->misc->signin_count_minimum > 10)
			$config->misc->signin_count_minimum = 3 ;
//die();
        return $this->render('SystemBundle:Install:misc.html.twig', array('config' => $config));
 	}
	
	public function miscSaveAction(Request $request)
	{
		$csrf = $this->get('security.csrf.token_manager');
		if (! $csrf->isTokenValid(new CsrfToken('miscellaneous', $request->request->get('_csrf_token')))) die();

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
		else
		{
			if ($valid)
				$this->get('session')->getFlashBag()->set('success', 'success.save.parameters');
			return new RedirectResponse($this->generateUrl('install_misc_check'));
		}
	}
	
	public function buildAction(Request $request)
	{
		$em = $this->get('doctrine')->getManager('default');
		$x = $this->get('doctrine')->getManager('dynamic');
    	$newEm = EntityManager::create($x->getConnection(), $em->getConfiguration());
		$meta = $em->getMetadataFactory()->getAllMetadata();	

		$tool = new SchemaTool($newEm);
		$tool->createSchema($meta);

		$repos = $newEm->getRepository('BusybeeSecurityBundle:Role');

        $this->entity = $this->findOrCreateRole('ROLE_USER', $newEm);
       	if (intval($this->entity->getId()) == 0) {
			$newEm->persist($this->entity);
			$newEm->flush();
		}

        $this->entity = $this->findOrCreateRole('ROLE_ALLOWED_TO_SWITCH', $newEm);
       	if (intval($this->entity->getId()) == 0) {
			$newEm->persist($this->entity);
			$newEm->flush();
		}
		
        $this->entity = $this->findOrCreateRole('ROLE_PARENT', $newEm);
       	if (intval($this->entity->getId()) == 0) {
			$newEm->persist($this->entity);
			$newEm->flush();
		}
		
        $this->entity = $this->findOrCreateRole('ROLE_STUDENT', $newEm);
       	if (intval($this->entity->getId()) == 0) {
			$newEm->persist($this->entity);
			$newEm->flush();
		}
		
        $this->entity = $this->findOrCreateRole('ROLE_TEACHER', $newEm);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addChildrenRole($repos->findOneBy(array('role' => 'ROLE_STUDENT')));
			$this->entity->addChildrenRole($repos->findOneBy(array('role' => 'ROLE_ALLOWED_TO_SWITCH')));
			$newEm->persist($this->entity);
			$newEm->flush();
		}
		
        $this->entity = $this->findOrCreateRole('ROLE_HEAD_TEACHER', $newEm);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addChildrenRole($repos->findOneBy(array('role' => 'ROLE_TEACHER')));
			$newEm->persist($this->entity);
			$newEm->flush();
		}
		
        $this->entity = $this->findOrCreateRole('ROLE_PRINCIPAL', $newEm);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addChildrenRole($repos->findOneBy(array('role' => 'ROLE_HEAD_TEACHER')));
			$newEm->persist($this->entity);
			$newEm->flush();
		}
		
        $this->entity = $this->findOrCreateRole('ROLE_ADMIN', $newEm);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addChildrenRole($repos->findOneBy(array('role' => 'ROLE_PARENT')));
			$this->entity->addChildrenRole($repos->findOneBy(array('role' => 'ROLE_ALLOWED_TO_SWITCH')));
			$newEm->persist($this->entity);
			$newEm->flush();
		}
		
        $this->entity = $this->findOrCreateRole('ROLE_REGISTRAR', $newEm);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addChildrenRole($repos->findOneBy(array('role' => 'ROLE_PRINCIPAL')));
			$this->entity->addChildrenRole($repos->findOneBy(array('role' => 'ROLE_ADMIN')));
			$newEm->persist($this->entity);
			$newEm->flush();
		}
		
        $this->entity = $this->findOrCreateRole('ROLE_SYSTEM_ADMIN', $newEm);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addChildrenRole($repos->findOneBy(array('role' => 'ROLE_REGISTRAR')));
			$newEm->persist($this->entity);
			$newEm->flush();
		}
		
        $this->entity = $this->findOrCreateRole('ROLE_STAFF', $newEm);
       	if (intval($this->entity->getId()) == 0) {
			$newEm->persist($this->entity);
			$newEm->flush();
		}

        $this->entity = $newEm->getRepository('BusybeeSecurityBundle:User')->find(1);
		if (is_null($this->entity))
			$this->entity = new User();
		$user = $this->getParameter('user');
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
			$repos = $newEm->getRepository('BusybeeSecurityBundle:Role');
			$this->entity->addDirectrole($repos->findOneBy(['role' => 'ROLE_SYSTEM_ADMIN']));
			$encoder = $this->get('security.password_encoder');
			$encoded = $encoder->encodePassword($this->entity, $user['password']);
			$this->entity->setPassword($encoded); 
	
			$newEm->persist($this->entity);
			$newEm->flush(); 
		}

		$repos = $newEm->getRepository('BusybeeSecurityBundle:Role');


        $this->entity = $this->findOrCreateGroup('Parent', $newEm);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addRole($repos->findOneBy(array('role' => 'ROLE_PARENT')));
			$newEm->persist($this->entity);
			$newEm->flush();
		}
		
        $this->entity = $this->findOrCreateGroup('Student', $newEm);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addRole($repos->findOneBy(array('role' => 'ROLE_STUDENT')));
			$newEm->persist($this->entity);
			$newEm->flush();
		}
		
        $this->entity = $this->findOrCreateGroup('Teaching Staff', $newEm);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addRole($repos->findOneBy(array('role' => 'ROLE_TEACHER')));
			$newEm->persist($this->entity);
			$newEm->flush();
		}
		
        $this->entity = $this->findOrCreateGroup('Non Teaching Staff', $newEm);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addRole($repos->findOneBy(array('role' => 'ROLE_STAFF')));
			$newEm->persist($this->entity);
			$newEm->flush();
		}
		
        $this->entity = $this->findOrCreateGroup('Contact', $newEm);
       	if (intval($this->entity->getId()) == 0) {
			$newEm->persist($this->entity);
			$newEm->flush();
		}
		
		
		$w = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir().'/config/config.yml'));

		$w['doctrine']['orm']['entity_managers']['default']['connection'] = 'normal';
		$w['doctrine']['orm']['entity_managers']['dynamic']['connection'] = 'install';
		
		file_put_contents($this->get('kernel')->getRootDir().'/config/config.yml', Yaml::dump($w));
		
		$w = Yaml::parse(file_get_contents($this->get('kernel')->getRootDir().'/config/parameters.yml'));

		unset($w['parameters']['user']);
	
		file_put_contents($this->get('kernel')->getRootDir().'/config/parameters.yml', Yaml::dump($w));
		
		$this->get('session')->getFlashBag()->add('success', 'buildDatabase.success');

		return new RedirectResponse($this->generateUrl('home_page'));
	}

  /**
     * Helper method to return an already existing Group from the database, else create and return a new one
     *
     * @param string        $name
     * @param ObjectManager $newEm
     *
     * @return Group
     */
    protected function findOrCreateGroup($name, $newEm)
    {
        return $newEm->getRepository('BusybeeSecurityBundle:Group')->findOneBy(['groupname' => $name]) ?: new Group($name);
    }
	
  /**
     * Helper method to return an already existing Role from the database, else create and return a new one
     *
     * @param string        $name
     * @param ObjectManager $newEm
     *
     * @return Role
     */
    protected function findOrCreateRole($role, $newEm)
    {
        return $newEm->getRepository('BusybeeSecurityBundle:Role')->findOneBy(['role' => $role]) ?: new Role($role);
    }

  /**
     * Helper method to return an already existing User from the database, else create and return a new one
     *
     * @param string        $name
     * @param ObjectManager $newEm
     *
     * @return Group
     */
    protected function findOrCreateUser($name, $newEm)
    {
        return $newEm->getRepository('BusybeeSecurityBundle:User')->findOneBy(['username' => $name]) ?: new User($name);
    }
}