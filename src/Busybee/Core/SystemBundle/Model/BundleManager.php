<?php

namespace Busybee\Core\SystemBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
	 * BundleManager constructor.
	 *
	 * @param Kernel $kernel
	 */
	public function __construct(Kernel $kernel)
	{
		$this->bundleFileName = $kernel->getProjectDir() . '/app/config/bundles.yml';
		$parameters           = Yaml::parse(file_get_contents($this->bundleFileName));
		$bundles              = $parameters['parameters']['bundles'];
		$this->bundles        = new ArrayCollection();
		foreach ($bundles as $name => $bundle)
		{
			$this->bundles->set($name, new Bundle($name, $bundle));
		}

		$this->help = "# This file contains the details of all Busybee Bundles.
# Format is:
#   name:
#       active:  true or false (Defaults to false)
#       type: (core or plugin) (Defaults to plugin)
#       namespace: namespace of the Bundle file
#       route: is an array of  (not compulsory if bundle has no routes.)
#           resource: location of routing file
#           type: Type of file (Default yaml) yaml, xml, php (if using annotations do not supply a route
#           prefix: added as a prefix to the path of the URL
";
	}

	/**
	 * @param $data
	 */
	public function saveBundles($data)
	{
		dump($data);
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
}