<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Yaml\Yaml;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
	        new Core23\DompdfBundle\Core23DompdfBundle(),
	        new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
	        new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
	        new Symfony\Bundle\AsseticBundle\AsseticBundle(),
	        new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
	        new Symfony\Bundle\MonologBundle\MonologBundle(),
	        new Symfony\Bundle\SecurityBundle\SecurityBundle(),
	        new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
	        new Symfony\Bundle\TwigBundle\TwigBundle(),);

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

	    $parameters = Yaml::parse(file_get_contents($this->getConfigDir() . '/parameters.yml'));

	    foreach ($parameters['parameters']['bundles'] as $bundle)
	    {
		    if ($bundle['active'] || $bundle['type'] === 'core')
			    $bundles[] = new $bundle['namespace']();
	    }

		return $bundles;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getProjectDir()
    {
        return dirname(__DIR__);
    }

	public function getLogDir()
	{
		return dirname(__DIR__) . '/var/logs';
	}

	public function getConfigDir()
	{
		return dirname(__DIR__) . '/app/config';
	}

	public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }

	public function getCharset()
	{
		return 'utf-8';
	}
}
