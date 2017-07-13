<?php
namespace Busybee\SystemBundle\Update ;

use Busybee\SystemBundle\Entity\Setting;
use Busybee\SystemBundle\Setting\SettingManager;
use Doctrine\ORM\EntityManager;
use stdClass ;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\Tools\SchemaTool ;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Update Manager
 *
 * @version	23rd October 2016
 * @since	23rd October 2016
 * @author	Craig Rayner
 */
class UpdateManager
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Version
     */
    private $version;

    /**
     * @var SettingManager
     */
    private $sm;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var    User
     */
    private $user;

    /**
     * Constructor
     *
     * @version 23rd October 2016
     * @since   23rd October 2016
     * @param   Symfony Container
     * @return  UpdateManager
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->version = new stdClass();
        $this->version->shouldBe = $this->container->getParameter('version');
        $this->sm = $this->container->get('setting.manager');
        $this->user = $this->container->get('grab.user.current');
        $this->sm->setCurrentUser($this->user);
        $this->version->current = array();
        $this->version->current['system'] = $this->sm->get('version.system', '0.0.00');
        $this->version->current['database'] = $this->sm->get('version.database', '0.0.00');
        return $this;
    }

    /**
     * get Version
     *
     * @version    23rd October 2016
     * @since    23rd October 2016
     * @param    Symfony Container
     * @return    stdClass
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * get Update Details
     *
     * @version 23rd October 2016
     * @since   23rd October 2016
     * @return  integer
     */
    public function getUpdateDetails()
    {
        $em = $this->container->get('doctrine')->getManager();

        $schemaTool = new SchemaTool($em);

        $metaData = $em->getMetadataFactory()->getAllMetadata();

        $xx = $schemaTool->getUpdateSchemaSql($metaData, false);

        $count = count($xx);

        $sysVersion = $this->version->current['system'];

        while (version_compare($sysVersion, $this->version->shouldBe['system'], '<')) {
            $v = '../src/Busybee/SystemBundle/Resources/config/updates/Setting_' . str_replace('.', '_', $sysVersion) . '.yml';

            if (file_exists($v))
                $count += $this->getCount($v);

            $sysVersion = $this->incrementVersion($sysVersion);
        }

        return $count;
    }

    /**
     * get Count
     *
     * @version 12th March 2017
     * @since   12th March 2017
     * @return  integer
     */
    private function getCount($fName)
    {
        $data = $this->loadSettingFile($fName);
        return count($data);
    }

    /**
     * load Setting File
     *
     * @version 12th March 2017
     * @since   12th March 2017
     * @return  array
     * @throws  ParseException
     */
    private function loadSettingFile($fName)
    {
        try {
            $data = Yaml::parse(file_get_contents($fName));
        } catch (ParseException $e) {
            $this->container->get('session')->getFlashBag()->add('error', $this->container->get('translator')->trans('updateDatabase.failure', array('%fName%' => $fName), 'SystemBundle'));
            return array();
        }
        return $data;
    }

    /**
     * increment Version
     *
     * @version    20th October 2016
     * @since    20th October 2016
     * @param    string $version
     * @return    string Version
     */
    private function incrementVersion($version)
    {
        $v = explode('.', $version);
        if (!isset($v[2])) $v[2] = 0;
        if (!isset($v[1])) $v[1] = 0;
        if (!isset($v[0])) $v[0] = 0;
        while (count($v) > 3)
            array_pop($v);
        $v[2]++;
        if ($v[2] > 99) {
            $v[2] = 0;
            $v[1]++;
        }
        if ($v[1] > 9) {
            $v[1] = 0;
            $v[0]++;
        }
        $v[2] = str_pad($v[2], 2, '00', STR_PAD_LEFT);
        return implode('.', $v);
    }

    /**
     * build
     *
     * @version 23rd October 2016
     * @since   23rd October 2016
     * @return  void
     */
    public function build()
    {

        $this->em = $this->container->get('doctrine')->getManager();

        $schemaTool = new SchemaTool($this->em);
        $metaData = $this->em->getMetadataFactory()->getAllMetadata();

        $schemaTool->updateSchema($metaData, true);

        $sysVersion = $this->version->current['system'];

        while (version_compare($sysVersion, $this->version->shouldBe['system'], '<')) {
            $v = '../src/Busybee/SystemBundle/Resources/config/updates/Setting_' . str_replace('.', '_', $sysVersion) . '.yml';
            if (file_exists($v))
                $this->buildSettings($this->loadSettingFile($v), $sysVersion);

            $sysVersion = $this->incrementVersion($sysVersion);
        }

        $this->loadSettings();

        $this->sm->setSetting('Version.System', $this->version->shouldBe['system']);
        $this->sm->setSetting('Version.Database', $this->version->shouldBe['database']);

        $this->version->current['system'] = $this->version->shouldBe['system'];
        $this->version->current['database'] = $this->version->shouldBe['database'];

    }

    /**
     * @param $data
     */
    private function buildSettings($data, $sysVersion)
    {
        if (empty($data))
            return;
        foreach ($data as $name => $datum) {
            $entity = new Setting();
            $entity->setName($name);
            foreach ($datum as $field => $value) {
                $w = 'set' . ucwords($field);
                $entity->$w($value);
            }
            $this->sm->createSetting($entity);
        }
        $this->container->get('session')->getFlashBag()->add('success', $this->container->get('translator')->trans('updateDatabase.success', array('%version%' => $sysVersion), 'SystemBundle'));
    }

    /**
     * @return bool
     */
    private function loadSettings()
    {
        $overWrite = $this->sm->get('Settings.Default.Overwrite', '');
        $file = '../src/Busybee/SystemBundle/Resources/Defaults/Default.yml';
        $content = file_get_contents($file);
        $content = Yaml::parse($content);
        $sess = $this->container->get('session');
        $sm = $this->container->get('setting.manager');
        if (empty($content['name']) || $content['name'] !== 'Default.yml') {
            $sess->getFlashBag()
                ->add('warning', array('upload.error.fileNameMatch' => array('%name%' => 'Default.yml')));;
            return false;
        }
        if (empty($content['settings'])) {
            $sess
                ->getFlashBag()
                ->add('error', 'upload.error.settingsMissing');
            return false;
        }
        if (empty($errors)) {
            foreach ($content['settings'] as $name => $value)
                $sm->set($name, $value);
            $sess
                ->getFlashBag()
                ->add('success', array('upload.success.default' => array('%count%' => count($content['settings']))));
        }

        if (!empty($overWrite)) {
            $file = $overWrite;
            $content = file_get_contents($file);
            $content = Yaml::parse($content);
            if (empty($content['name']) || $content['name'] !== basename($file)) {
                $sess->getFlashBag()
                    ->add('warning', array('upload.error.fileNameMatch' => array('%name%' => basename($file))));;
                return false;
            }
            if (empty($content['settings'])) {
                $sess
                    ->getFlashBag()
                    ->add('error', 'upload.error.settingsMissing');
                return false;
            }
            if (empty($errors)) {
                foreach ($content['settings'] as $name => $value)
                    $sm->set($name, $value);
                $sess
                    ->getFlashBag()
                    ->add('success', array('upload.success.overwrite' => array('%count%' => count($content['settings']))));
                return true;
            }
        }
    }
}