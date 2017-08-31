<?php

namespace Busybee\Core\SystemBundle\Model;

use Busybee\Core\HomeBundle\Exception\Exception;
use Busybee\Core\SystemBundle\Entity\Setting;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Yaml\Yaml;

class BundleManager
{
	/**
	 * @var string
	 */
	private $bundleFileName;

	/**
	 * @var ArrayCollection
	 */
	private $bundles;

	/**
	 * @var string
	 */
	private $help;

	/**
	 * @var array
	 */
	private $messages;

	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * @var array
	 */
	private $objectManager;

	/**
	 * @var string
	 */
	private $projectDir;

	/**
	 * BundleManager constructor.
	 *
	 * @param Kernel $kernel
	 */
	public function __construct(Kernel $kernel)
	{
		$this->bundleFileName = $kernel->getProjectDir() . '/app/config/bundles.yml';
		$parameters           = Yaml::parse(file_get_contents($this->bundleFileName));
		$bundles              = $parameters['parameters']['bundles'];
		$this->settingManager = $kernel->getContainer()->get('setting.manager');
		$this->bundles        = new ArrayCollection();

		foreach ($bundles as $name => $bundle)
		{
			$this->bundles->set($name, new Bundle($name, $bundle));
		}

		$this->projectDir = $kernel->getProjectDir();

		$this->help          = "# This file contains the details of all Busybee Bundles.
# Format is:
#       name: Name of the Bundle
#       active:  true or false (Defaults to false)
#       type: (core or plugin) (Defaults to plugin)
#       namespace: namespace of the Bundle file
#       route: is an array of  (not compulsory if bundle has no routes.)
#           resource: location of routing file
#           type: Type of file (Default yaml) yaml, xml, php (if using annotations do not supply a route
#           prefix: added as a prefix to the path of the URL
#       description: A description of the Bundle (use a translation phrase of 'bundle.description.name'
#       requirements: array of Bundles that are required to be active if this bundle is active.
#       exclusions: array of Bundle that must not be active if this bundle is active.
";
		$this->messages      = [];
		$this->objectManager = $kernel->getContainer()->get('doctrine')->getManager();
	}

	/**
	 * Save Bundles to YAML File
	 */
	public function saveBundles()
	{
		$data                          = [];
		$data['parameters']            = [];
		$data['parameters']['bundles'] = [];

		foreach ($this->bundles->toArray() as $name => $bundle)
		{
			$x              = [];
			$x['name']      = $bundle->getName();
			$x['active']    = $bundle->isActive();
			$x['type']      = $bundle->getType();
			$x['namespace'] = $bundle->getNamespace();
			if ($bundle->isRoute())
			{
				$x['route']             = [];
				$x['route']['resource'] = $bundle->getRouteResource();
				$x['route']['type']     = $bundle->getRouteType();
				$x['route']['prefix']   = trim($bundle->getRoutePrefix(), '/');
			}
			$x['description']                     = $bundle->getDescription();
			$x['exclusions']                      = $bundle->getExclusions();
			$x['requirements']                    = $bundle->getRequirements();
			$data['parameters']['bundles'][$name] = $x;
		}

		try
		{
			file_put_contents($this->bundleFileName, $this->help . Yaml::dump($data));
		}
		catch (\Exception $e)
		{
			$this->addMessage('danger', 'bundle.activate.save.failure', ['%message%' => empty($e->getMessage()) ? '
			Empty' : $e->getMessage()]);

			return;
		}
		$this->addMessage('success', 'bundle.activate.save.success');
	}

	/**
	 * Bundle List
	 *
	 * @param bool $withCore
	 *
	 * @return array
	 */
	public function getBundleList($withCore = false): array
	{
		$list = [];
		foreach ($this->getBundles()->toArray() as $name => $bundle)
			if (!$bundle->isCore() || $withCore)
				$list[$name] = $name;

		return $list;
	}

	/**
	 * @return Collection
	 */
	public function getBundles(): ArrayCollection
	{
		return $this->bundles;
	}

	/**
	 * @param Collection $bundles
	 */
	public function setBundles(ArrayCollection $bundles)
	{
		$this->bundles = $bundles;
	}

	public function handleRequest(FormInterface $form, Request $request)
	{
		$data = $request->request->get('bundles_manage');
		if (empty($data))
			return [];
		// Do any sort stuff here ...
		$formData = $form->getData();

		$data    = $data['bundles'];
		$w       = new ArrayCollection();
		$bundles = $formData->getBundles();

		foreach ($data as $q => $bundle)
			$w->set($data[$q]['name'], $bundles->get($data[$q]['name']));

		$bundles = $w;

		foreach ($data as $q => $w)
		{
			$bundle = $bundles->get($data[$q]['name']);
			if (empty($w['active']) || $w['active'] !== 'on')
			{
				$data[$q]['active']  = false;
				$data[$q]['changed'] = false;
				if ($bundle->isActive())
					$data[$q]['changed'] = true;
				//check for required clash
				if ($this->isRequired($bundle->getName()))
					return;
			}
			else
			{
				$data[$q]['changed'] = false;
				$data[$q]['active']  = true;
				if (!$bundle->isActive())
					$data[$q]['changed'] = true;
				//check for Exclusion Clash
				if ($this->isExcluded($bundle->getName()))
					return;
			}

			//check for Core
			if ($bundle->isCore())
			{
				$data[$q]['changed'] = false;
				$data[$q]['active']  = true;
			}


			// remove those bundles that did not change.
			if (!$data[$q]['changed'])
				unset($data[$q]);
			else
				$bundle->setActive($data[$q]['active']);
		}

		$formData->setBundles($bundles);
		$form->setData($formData);

		// Now it is time to save...
		foreach ($data as $bundle)
			$this->addMessage('success', 'bundle.activate.success', ['%name%' => $bundle['name']]);

		$this->saveBundles();
	}

	/**
	 * @param string $bundleName
	 *
	 * @return bool
	 */
	public function isExcluded(string $bundleName)
	{

		$bundle = $this->getBundleByName(($bundleName));

		foreach ($bundle->getExclusions() as $name)
		{
			if ($this->bundles->get($name)->isActive())
			{
				$this->addMessage('warning', 'bundle.activate.excluded', ['%name%' => $bundleName, '%conflict%' => $this->bundles->get($name)->getName()]);

				return true;
			}
		}

		foreach ($this->bundles->toArray() as $tb)
			if ($tb->isActive() && in_array($bundleName, $tb->getExclusions()))
			{
				$this->addMessage('warning', 'bundle.activate.excluded', ['%name%' => $bundleName, '%conflict%' => $tb->getName()]);

				return true;
			}

		return $this->requiredExclusion($bundle);
	}

	/**
	 * @param       $level
	 * @param       $message
	 * @param array $options
	 *
	 * @return $this
	 */
	private function addMessage($level, $message, $options = [])
	{
		$mess = new Message();

		$mess->setDomain('SystemBundle');
		$mess->setLevel($level);
		$mess->setMessage($message);
		foreach ($options as $name => $element)
			$mess->addOption($name, $element);

		$this->messages[] = $mess;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getMessages()
	{
		return $this->messages;
	}

	/**
	 * @param $name
	 *
	 * @return Bundle
	 */
	private function getBundleByName($name)
	{
		return $this->bundles->get($name);
	}

	private function requiredExclusion(Bundle $bundle)
	{
		$excluded = false;

		foreach ($bundle->getRequirements() as $name)
		{
			if ($this->isExcluded($name))
			{
				$excluded = true;
				break;
			}
		}

		return $excluded;
	}

	/**
	 * Check to see if a bundle can be made inactive.
	 *
	 * @param $name
	 *
	 * @return bool
	 */
	public function isRequired($name)
	{
		$bundle = $this->getBundleByName($name);

		foreach ($this->bundles->toArray() as $tb)
		{
			if ($tb->getName() !== $bundle->getName())
			{
				if ($tb->isActive())
				{
					foreach ($tb->getRequirements() as $w)
					{
						if ($w === $bundle->getName() && $this->getBundleByName($w)->isActive())
						{
							$this->addMessage('warning', 'bundle.activate.required', ['%name%' => $bundle->getName(), '%conflict%' => $tb->getName()]);

							return true;
						}
					}
				}
			}
		}

		return false;
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public function updateRequired($name)
	{

		if (!$this->settingManager->hasParameter($name))
			return false;

		$bundle = $this->getBundleByName($name);
		if (!$bundle->isActive())
			return false;
		$bundleParams = $this->settingManager->getParameter($name);

		$current   = $this->settingManager->get($name . '.version', '0.0.00');
		$available = isset($bundleParams['version']) ? $bundleParams['version'] : '0.0.00';

		$this->updateDetails = ['%available%' => $available, '%current%' => $current];

		return version_compare($available, $current, '>');
	}

	/**
	 * @var array
	 */
	private $updateDetails;

	/**
	 * @return array
	 */
	public function getUpdateDetails(): array
	{
		return empty($this->updateDetails) ? ['%available%' => '', '%current%' => ''] : $this->updateDetails;
	}

	/**\
	 * Update Bundle
	 *
	 * @param      $name
	 * @param bool $requiredWarning
	 */
	public function updateBundle($name, $requiredWarning = true)
	{
		if (!$this->updateRequired($name))
		{
			if ($requiredWarning)
				$this->addMessage('warning', 'bundle.activate.update.notrequired', ['%name%' => $name]);

			return;
		}

		$bundleParams = $this->settingManager->getParameter($name);
		$bv           = $this->settingManager->get($name . '.version', '0.0.00');

		if (isset($bundleParams['settings']) && isset($bundleParams['settings']['resources']))
		{
			$resources = is_array($bundleParams['settings']['resources']) ? $bundleParams['settings']['resources'] : [$bundleParams['settings']['resources']];

			foreach ($resources as $resource)
			{
				$data    = $this->load('@' . $resource);
				$version = empty($data[$name . '.version']) ? false : $data[$name . '.version'];
				if ($version === false)
				{
					$this->addMessage('warning', 'bundle.update.resource.misconfigured', ['%name%' => $resource]);

					return;
				}
				$version = empty($version['value']) ? false : $version['value'];
				if ($version === false)
				{
					$this->addMessage('warning', 'bundle.update.resource.misconfigured', ['%name%' => $resource]);

					return;
				}
				if (version_compare($version, $bv, '>='))
					$this->buildSettings($data, $resource);
				else
					$this->addMessage('info', 'bundle.update.resource.old', ['%name%' => $resource]);
			}
		}
		else
			$this->addMessage('warning', 'bundle.update.resource.missing', ['%name%' => $name]);


	}


	/**
	 * @param $data
	 */
	private function buildSettings($data, $resource)
	{
		if (empty($data))
			return;
		foreach ($data as $name => $datum)
		{
			$entity = new Setting();
			$entity->setName($name);
			foreach ($datum as $field => $value)
			{
				$w = 'set' . ucwords($field);
				$entity->$w($value);
			}
			$this->settingManager->createSetting($entity);
		}

		$this->addMessage('success', 'bundle.update.resource.success', ['%resource%' => $resource]);
	}

	/**
	 * @param $resource
	 *
	 * @return array|mixed
	 */
	private function load($resource)
	{
		$res = explode('/', str_replace('@', '', $resource));

		$w   = $this->settingManager->getParameter('kernel.bundles')[$res[0]];
		$w   = explode('\\', $w);
		array_pop($w);
		$w = implode('/', $w);

		$res[0]   = $this->projectDir . '/src/' . str_replace('\\', '/', $w);
		$resource = implode('/', $res);

		if (file_exists(realpath($resource)))
			return Yaml::parse(file_get_contents($resource));

		$this->addMessage('warning', 'bundle.update.resource.notavailable', ['%resource%' => $resource]);

		return [];
	}

	/**
	 * get SQL Count
	 *
	 * @version 23rd October 2016
	 * @since   23rd October 2016
	 * @return  integer
	 */
	public function getSQLCount(): int
	{
		$schemaTool = new SchemaTool($this->objectManager);

		$metaData = $this->objectManager->getMetadataFactory()->getAllMetadata();

		$xx = $schemaTool->getUpdateSchemaSql($metaData, true);

		return count($xx);
	}

	/**
	 * Build Database
	 *
	 * @version 30th August 2017
	 * @since   23rd October 2016
	 * @return  void
	 */
	public function buildDatabase()
	{
		$schemaTool = new SchemaTool($this->objectManager);

		$metaData = $this->objectManager->getMetadataFactory()->getAllMetadata();

		$xx = $schemaTool->getUpdateSchemaSql($metaData, true);

		$count = count($xx);

		$schemaTool->updateSchema($metaData, true);

		$this->addMessage('success', 'bundle.update.database.success', ['%count%' => $count]);
	}

	/**
	 * Upadte All Bundles
	 */
	public function updateAllBundles()
	{
		foreach ($this->getBundleList(true) as $name)
			$this->updateBundle($name, false);
	}

	/**
	 * Any Update Required
	 *
	 * @return bool
	 */
	public function anyUpdateRequired()
	{
		foreach ($this->getBundleList(true) as $name)
			if ($this->updateRequired($name))
				return true;

		return false;
	}

}