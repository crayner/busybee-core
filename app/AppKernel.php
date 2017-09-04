<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Yaml\Yaml;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
	    $bundles = [
	        new Core23\DompdfBundle\Core23DompdfBundle(),
	        new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
	        new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
	        new Symfony\Bundle\AsseticBundle\AsseticBundle(),
	        new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
	        new Symfony\Bundle\MonologBundle\MonologBundle(),
	        new Symfony\Bundle\SecurityBundle\SecurityBundle(),
	        new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
		    new Symfony\Bundle\TwigBundle\TwigBundle(),
	    ];

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

	    if (file_exists($this->getConfigDir() . '/bundles.yml'))
	    {
		    $parameters = Yaml::parse(file_get_contents($this->getConfigDir() . '/bundles.yml'));
		    foreach ($parameters as $bundle)
		    {
			    // Core must be loaded manually above.
			    if ((isset($bundle['active']) && $bundle['active']) || $bundle['type'] === 'core')
				    $bundles[] = new $bundle['namespace']();
		    }
	    }
	    else
	    {
		    $bundles[] = new Busybee\Core\InstallBundle\BusybeeInstallBundle();
		    $bundles[] = new Busybee\Core\TemplateBundle\BusybeeTemplateBundle();
	    }
		return $bundles;
    }

    public function getCacheDir()
    {
	    return $this->getProjectDir() . '/var/cache/' . $this->getEnvironment();
    }

	/*
		public function getProjectDir()
		{
			return dirname(__DIR__);
		}
	*/
	public function getLogDir()
	{
		return $this->getProjectDir() . '/var/logs';
	}

	public function getConfigDir()
	{
		return $this->getProjectDir() . '/app/config';
	}

	public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
