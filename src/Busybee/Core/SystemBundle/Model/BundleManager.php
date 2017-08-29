<?php

namespace Busybee\Core\SystemBundle\Model;

use Busybee\Core\HomeBundle\Exception\Exception;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
	 * BundleManager constructor.
	 *
	 * @param Kernel $kernel
	 */
	public function __construct(Kernel $kernel)
	{
		$this->bundleFileName = $kernel->getProjectDir() . '/app/config/bundles.yml';
		$parameters           = Yaml::parse(file_get_contents($this->bundleFileName));
		$bundles              = $parameters['parameters']['bundles'];

		$this->bundles = new ArrayCollection();

		foreach ($bundles as $name => $bundle)
		{
			$this->bundles->set($name, new Bundle($name, $bundle));
		}

		$this->help     = "# This file contains the details of all Busybee Bundles.
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
		$this->messages = [];
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
			$this->addMessage('danger', 'bundle.activate.save.failure');

			return;
		}
		$this->addMessage('success', 'bundle.activate.save.success');
	}

	public function getBundleList()
	{
		$list = [];
		foreach ($this->getBundles()->toArray() as $bundle)
			if (!$bundle->isCore())
				$list[$bundle->getName()] = $bundle->getName();

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
}