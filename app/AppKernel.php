<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
			new Core23\DompdfBundle\Core23DompdfBundle(),
	        new Busybee\Core\HomeBundle\BusybeeHomeBundle(),
            new Busybee\SecurityBundle\BusybeeSecurityBundle(),
	        new Busybee\Core\SystemBundle\SystemBundle(),
	        new Busybee\Core\FormBundle\BusybeeFormBundle(),
            new Busybee\PaginationBundle\PaginationBundle(),
	        new Busybee\People\PersonBundle\BusybeePersonBundle(),
            new Busybee\CurriculumBundle\BusybeeCurriculumBundle(),
	        new Busybee\People\StaffBundle\BusybeeStaffBundle(),
            new Busybee\StudentBundle\BusybeeStudentBundle(),
            new Busybee\InstituteBundle\BusybeeInstituteBundle(),
            new Busybee\FamilyBundle\BusybeeFamilyBundle(),
            new Busybee\TimeTableBundle\BusybeeTimeTableBundle(),
            new Busybee\ActivityBundle\BusybeeActivityBundle(),
            new Busybee\AttendanceBundle\BusybeeAttendanceBundle(),
            new Busybee\PeriodBundle\BusybeePeriodBundle(),
	        new Busybee\Core\CalendarBundle\BusybeeCalendarBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

	    $searchPath = __DIR__ . '/../vendor/busybee';
	    if (is_dir($searchPath))
		    foreach (new DirectoryIterator($searchPath) as $fileInfo)
		    {


			    if ($fileInfo->isDot()) continue;
			    if ($fileInfo->isDir())
			    {
				    $plugin = ucfirst(str_replace('Bundle', '', $fileInfo->getFileName()));

				    $bundle    = 'Busybee' . $plugin . '\\Busybee' . $plugin . 'Bundle()';
				    $bundles[] = new $bundle;
			    }
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
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
