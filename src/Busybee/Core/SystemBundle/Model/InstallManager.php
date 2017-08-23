<?php

namespace Busybee\Core\SystemBundle\Model;

use Busybee\Core\SystemBundle\Password\PasswordManager;
use Doctrine\Bundle\DoctrineBundle\ConnectionFactory;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\SyntaxErrorException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

class InstallManager
{
	/**
	 * @var \stdClass
	 */
	public $sql;
	/**
	 * @var \stdClass
	 */
	public $mailer;
	/**
	 * @var \stdClass
	 */
	public $misc;
	/**
	 * @var string
	 */
	private $projectDir;
	/**
	 * @var ConnectionFactory
	 */
	private $factory;
	/**
	 * @var Connection
	 */
	private $connection;
	/**
	 * @var PasswordManager
	 */
	private $passwordManager;

	/**
	 * InstallManager constructor.
	 *
	 * @param $projectDir
	 */
	public function __construct($projectDir, ConnectionFactory $factory, PasswordManager $passwordManager)
	{
		$this->projectDir      = $projectDir;
		$this->factory         = $factory;
		$this->connection      = null;
		$this->sql             = new \stdClass();
		$this->passwordManager = $passwordManager;
	}

	/**
	 * Parameter Status
	 *
	 * @return bool
	 */
	public function parameterStatus()
	{
		return is_writable($this->projectDir . '/app/config/parameters.yml');
	}

	/**
	 * Get SQl Parameters
	 *
	 * @param $params
	 *
	 * @return array
	 */
	public function getSQLParameters($params)
	{
		$sql = [];
		foreach ($params as $name => $value)
			if (strpos($name, 'database_') === 0)
			{
				$this->sql->$name      = $value;
				$sql[substr($name, 9)] = $value;
			}

		return $sql;
	}

	/**
	 * Test Connected
	 *
	 * @param $sql
	 *
	 * @return mixed
	 */
	public function testConnected($sql)
	{
		unset($sql['name']);
		$this->sql->error = 'No Error Detected.';
		$this->connection = $this->factory->createConnection($sql);

		try
		{
			$this->connection->connect();
		}
		catch (ConnectionException | \Exception $e)
		{
			$this->sql->error     = $e->getMessage();
			$this->sql->connected = false;
			$this->exception      = $e;
		}
		$this->sql->connected = $this->connection->isConnected();

		return $this->sql->connected;
	}

	public function hasDatabase()
	{
		try
		{
			$this->connection->executeQuery("CREATE DATABASE IF NOT EXISTS " . $this->sql->database_name . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
		}
		catch (SyntaxErrorException $e)
		{
			$this->sql->error     = $e->getMessage() . '. <strong>The database name must not have any spaces.</strong>';
			$this->sql->connected = false;
			$this->exception      = $e;

		}

		if ($this->sql->connected)
			$this->connection->executeQuery("ALTER DATABASE `" . $this->sql->database_name . "` CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci`");

		return $this->sql->connected;
	}

	/**
	 * Handle Database Request
	 *
	 * @param FormInterface $form
	 * @param Request       $request
	 *
	 * @return array
	 */
	public function handleDataBaseRequest(FormInterface $form, Request $request)
	{

		$form->handleRequest($request);
		$this->saveDatabase = false;

		$sql = [];
		foreach ((array) $this->sql as $name => $value)
		{
			if (strpos($name, 'database_') === 0)
			{
				$sql[str_replace('database_', '', $name)] = $value;
			}
		}
		if (!$form->isSubmitted())
			return $sql;

		foreach ($sql as $name => $value)
		{
			$sql[$name]    = $form->get($name)->getData();
			$n             = 'database_' . $name;
			$this->sql->$n = $form->get($name)->getData();
		}

		if ($form->isValid())
		{
			$params = $this->getParameters();
			foreach ((array) $this->sql as $name => $value)
				if (strpos($name, 'database_') === 0)
					$params[$name] = $value;

			$this->saveDatabase = $this->saveParameters($params);

		}

		return $sql;
	}

	/**
	 * Get Parameters
	 *
	 * @return array
	 */
	public function getParameters()
	{
		$params = Yaml::parse(file_get_contents($this->projectDir . '/app/config/parameters.yml'));
		$params = $params['parameters'];

		return $params;
	}

	/**
	 * Save Parameters
	 *
	 * @param $params array
	 *
	 * @return bool
	 */
	public function saveParameters($params)
	{
		$w = [];
		if (isset($params['parameters']) && count($params) === 1)
			$w = $params;
		else
			$w['parameters'] = $params;

		if (file_put_contents($this->projectDir . '/app/config/parameters.yml', Yaml::dump($w)))
			return true;

		return false;
	}

	/**
	 * Get Config
	 *
	 * @return array
	 */
	public function getConfig()
	{
		$params = Yaml::parse(file_get_contents($this->projectDir . '/app/config/config.yml'));

		return $params;
	}

	/**
	 * Save Config
	 *
	 * @param $w array
	 *
	 * @return bool
	 */
	public function saveConfig($config)
	{
		$this->savedConfig = false;
		if (file_put_contents($this->projectDir . '/app/config/config.yml', Yaml::dump($config)))
			$this->savedConfig = true;

		return $this->savedConfig;
	}

	/**
	 * @param FormInterface $form
	 * @param Request       $request
	 *
	 * @return null
	 */
	public function handleMailerRequest(FormInterface $form, Request $request)
	{
		$form->handleRequest($request);
		$this->saveMailer = false;

		if (!$form->isSubmitted())
			return;

		if ($form->isValid())
		{
			$mailer = $this->getMailerParameters();
			$params = $this->getParameters();
			foreach ($mailer as $name => $value)
			{
				$this->mailer->$name       = $form->get($name)->getData();
				$params['mailer_' . $name] = trim($form->get($name)->getData());
				if (empty($params['mailer_' . $name]))
					$params['mailer_' . $name] = null;
			}
			if ($params['mailer_host'] === 'empty')
				$params['mailer_host'] = null;

			$this->saveMailer = $this->saveParameters($params);
		}

		return;
	}

	/**
	 * @return array
	 */
	public function getMailerParameters()
	{
		$this->mailer = new \stdClass();
		$mailer       = [];
		foreach ($this->getParameters() as $name => $value)
			if (strpos($name, 'mailer_') === 0)
			{
				$this->mailer->$name      = $value;
				$mailer[substr($name, 7)] = $value;
			}

		return $mailer;
	}

	/**
	 * @param FormInterface $form
	 * @param Request       $request
	 */
	public function handleMiscellaneousRequest(FormInterface $form, Request $request)
	{
		$misc          = $this->getMiscellaneousParameters();
		$this->proceed = false;
		$form->handleRequest($request);

		if (!$form->isSubmitted()) return;

		foreach ($misc as $name => $value)
		{
			switch ($name)
			{
				case 'systemUser':
					foreach ($value as $q => $w)
					{
						if (!in_array($q, ['text', 'password']))
							$this->misc->systemUser[$q] = $form->get($q)->getData();
						elseif ($q === 'password')
							$this->misc->systemUser[$q] = $form->get('pass_word')->getData();
					}
					break;
				case 'password':
					foreach ($value as $q => $w)
					{
						$this->misc->password[$q] = $form->get($q)->getData();
					}
					break;
				case 'google':
					foreach ($value as $q => $w)
					{
						$this->misc->google[$q] = $form->get($q)->getData();
					}
					break;
				default:
					$this->misc->$name = $form->get($name)->getData();
			}
		}

		if ($form->isValid())
		{

			$params = $this->getParameters();
			foreach ((array) $this->misc as $name => $value)
			{
				$params[$name] = $value;
			}
			$this->proceed = $this->saveParameters($params);
		}

		return;
	}

	/**
	 * @return array
	 */
	public function getMiscellaneousParameters()
	{
		$this->misc = new \stdClass();
		$params     = $this->getParameters();
		$misc       = [];


		$valueList = [
			'secret'                   => '',
			'locale'                   => '',
			'session_name'             => '',
			'session_remember_me_name' => '',
			'session_max_idle_time'    => '',
			'country'                  => '',
			'timezone'                 => '',
			'password'                 => [],
			'signin_count_minimum'     => '',
			'google'                   => [],
		];

		foreach ($valueList as $name => $value)
		{
			$this->misc->$name = $params[$name];
			$misc[$name]       = $params[$name];
		}

		$su = [
			'email',
			'username',
			'password',
			'text',
		];

		foreach ($su as $name)
		{
			$this->misc->systemUser[$name] = isset($params['systemUser'][$name]) ? $params['systemUser'][$name] : '';
			if ($name === 'text')
			{
				$x                              = [];
				$x['mixedCase']                 = $this->misc->password['mixedCase'];
				$x['minLength']                 = $this->misc->password['minLength'];
				$x['numbers']                   = $this->misc->password['numbers'];
				$x['specials']                  = $this->misc->password['specials'];
				$this->misc->systemUser['text'] = $this->passwordManager->buildPassword($x);
			}
			$misc['systemUser'][$name] = $this->misc->systemUser[$name];
		}

		return $misc;
	}
}