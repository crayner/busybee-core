<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Finder\Finder ;
use Symfony\Component\HttpFoundation\Request;

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
            new Busybee\HomeBundle\BusybeeHomeBundle(),
            new Busybee\SecurityBundle\BusybeeSecurityBundle(),
            new Busybee\SystemBundle\SystemBundle(),
            new Busybee\FormBundle\BusybeeFormBundle(),
            new Busybee\PaginationBundle\PaginationBundle(),
            new Busybee\PersonBundle\BusybeePersonBundle(),
            new Busybee\CurriculumBundle\BusybeeCurriculumBundle(),
            new Busybee\StaffBundle\BusybeeStaffBundle(),
            new Busybee\StudentBundle\BusybeeStudentBundle(),
            new Busybee\InstituteBundle\BusybeeInstituteBundle(),
            new Busybee\FamilyBundle\BusybeeFamilyBundle(),
            new Busybee\TimeTableBundle\BusybeeTimeTableBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

		$searchPath = __DIR__.'/../src/Busybee/Plugin';
        $finder     = new Finder();
        $finder->files()
               ->in($searchPath)
               ->name('*Bundle.php');
		
		foreach ($finder as $file) {
            $path       = substr($file->getRealpath(), strrpos($file->getRealpath(), "src/Busybee/Plugin") + 18);
            $parts      = array_merge(array('Busybee', 'Plugin'), explode('/', $path));
            $class      = array_pop($parts);
            $namespace  = str_replace('\\\\', '\\', implode('\\', $parts));
            $class      = str_replace('\\\\', '\\', $namespace.'\\'.$class);
            //remove first slash and .php
            $class = ltrim(str_replace('.php', '', $class), '\\');
//            $bundles[]  = new $class();
        }
        
		return $bundles;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    /**
     * @param Request $request
     * @param int $type
     * @param bool $catch
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Symfony\Component\HttpFoundation\Request $request, $type = \Symfony\Component\HttpKernel\HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        try {

            return parent::handle($request);

        } catch (Exception $e) {
            switch ($e->getErrorCode()) {
                case 1045:
                    echo $e->getMessage();
                    echo "<p>Check and correct settings in the parameters.yml file located in the  app/config/ directory.</p>";
                    die();
                    break;
                case 1049:
                    echo $e->getMessage();
                    echo "<p>The database does not exist.</p>";
                    var_dump($e);
                    die();
                    break;
                default:
                    echo $e->getErrorCode() . '<br/>';
                    echo $e->getMessage();
                    echo "<p>Hmm.  Fix something.</p>";
                    die();
            }
        }
    }
}
