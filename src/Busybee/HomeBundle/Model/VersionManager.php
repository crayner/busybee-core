<?php

namespace Busybee\HomeBundle\Model;


use Busybee\SystemBundle\Setting\SettingManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Version;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Translation\TranslatorInterface;

class VersionManager
{
    /**
     * @var SettingManager
     */
    private $settingManager;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var array
     */
    private $version;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * VersionManager constructor.
     *
     * @param Connection $connection
     * @param SettingManager $settingManager
     * @param $version
     * @param TranslatorInterface $translator
     */
    public function __construct(Connection $connection, SettingManager $settingManager, $version, TranslatorInterface $translator)
    {
        $this->connection = $connection;
        $this->settingManager = $settingManager;
        $this->version = $version;
        $this->translator = $translator;
        return $this;
    }

    /**
     * Get Version
     *
     * @return array
     */
    public function getVersion()
    {
        $versions = [];

        $versions['Busybee']['System'] = $this->settingManager->get('version.system');

        $versions['Twig'] = \Twig_Environment::VERSION;
        $versions['Symfony'] = Kernel::VERSION;
        $versions['Doctrine']['ORM'] = Version::VERSION;
        $versions['Doctrine']['Common'] = \Doctrine\Common\Version::VERSION;
        $versions['Database']['Server'] = $this->connection->getWrappedConnection()->getServerVersion();
        $versions['Database']['Driver'] = $this->connection->getParams()['driver'];
        $versions['Database']['Character Set'] = $this->connection->getParams()['charset'];
        $versions['Busybee']['Database'] = $this->settingManager->get('Version.Database');
        $versions['Doctrine']['DBal'] = \Doctrine\DBAL\Version::VERSION;

        foreach (get_loaded_extensions() as $name)
            $versions['PHP'][$name] = phpversion($name);

        foreach ($versions as $q => $w) {
            if (is_array($w)) {
                foreach ($w as $e => $r) {
                    unset($versions[$q][$e]);
                    $versions[$q][$e]['value'] = $r;
                    $versions[$q][$e]['flag'] = false;

                }
            } else {
                unset($versions[$q]);
                $versions[$q]['value'] = $w;
                $versions[$q]['flag'] = false;
            }
        }

        if ($versions['Busybee']['System']['value'] !== $this->version['system'])
            $versions['Busybee']['System']['flag'] = 'alert alert-warning';
        else
            $versions['Busybee']['System']['flag'] = 'alert alert-success';

        $phpVersions = [];
        $phpVersions['Core'] = '7.1.6';
        $phpVersions['apcu'] = '5.1.8';
        $phpVersions['intl'] = '1.1.0';
        $phpVersions['json'] = '1.5.0';
        $phpVersions['mbstring'] = '7.1.6';
        $phpVersions['PDO'] = '7.1.6';
        $phpVersions['gettext'] = '7.1.6';
        $phpVersions['pdo_mysql'] = '7.1.6';
        $phpVersions['Zend OPcache'] = '7.1.6';


        foreach ($phpVersions as $name => $version) {
            if (!isset($versions['PHP'][$name])) {
                $versions['PHP'][$name]['value'] = $this->translator->trans('software.required', ['%required%' => $version], 'BusybeeHomeBundle');
                $versions['PHP'][$name]['flag'] = 'alert alert-danger';

            } elseif (version_compare($versions['PHP'][$name]['value'], $version, '<')) {
                $versions['PHP'][$name]['flag'] = 'alert alert-warning';
                $versions['PHP'][$name]['value'] .= $this->translator->trans('version.upgrade', ['%required%' => $version], 'BusybeeHomeBundle');
            } elseif (version_compare($versions['PHP'][$name]['value'], $version, '=')) {
                $versions['PHP'][$name]['flag'] = 'alert alert-success';
            } elseif (version_compare($versions['PHP'][$name]['value'], $version, '>')) {
                $versions['PHP'][$name]['flag'] = 'alert alert-info';
                $versions['PHP'][$name]['value'] .= $this->translator->trans('version.over', ['%required%' => $version], 'BusybeeHomeBundle');
            }
        }

        $version = '5.6.37';
        if (version_compare($versions['Database']['Server']['value'], $version, '<')) {
            $versions['Database']['Server']['flag'] = 'alert alert-warning';
            $versions['Database']['Server']['value'] .= $this->translator->trans('version.upgrade', ['%required%' => $version], 'BusybeeHomeBundle');
        } elseif (version_compare($versions['Database']['Server']['value'], $version, '=')) {
            $versions['Database']['Server']['flag'] = 'alert alert-success';
        } elseif (version_compare($versions['Database']['Server']['value'], $version, '>')) {
            $versions['Database']['Server']['flag'] = 'alert alert-info';
            $versions['Database']['Server']['value'] .= $this->translator->trans('version.over', ['%required%' => $version], 'BusybeeHomeBundle');
        }

        $version = 'utf8mb4';
        if ($versions['Database']['Character Set']['value'] !== $version) {
            $versions['Database']['Character Set']['flag'] = 'alert alert-danger';
            $versions['Database']['Character Set']['value'] .= $this->translator->trans('setting.required', ['%required%' => $version], 'BusybeeHomeBundle');
        } else
            $versions['Database']['Character Set']['flag'] = 'alert alert-success';

        $version = 'pdo_mysql';
        if ($versions['Database']['Driver']['value'] !== $version) {
            $versions['Database']['Driver']['flag'] = 'alert alert-danger';
            $versions['Database']['Driver']['value'] .= $this->translator->trans('setting.required', ['%required%' => $version], 'BusybeeHomeBundle');
        } else
            $versions['Database']['Driver']['flag'] = 'alert alert-success';

        $version = '3.3.5';
        if (version_compare($versions['Symfony']['value'], $version, '<')) {
            $versions['Symfony']['flag'] = 'alert alert-warning';
            $versions['Symfony']['value'] .= $this->translator->trans('version.upgrade', ['%required%' => $version], 'BusybeeHomeBundle');
        } elseif (version_compare($versions['Symfony']['value'], $version, '=')) {
            $versions['Symfony']['flag'] = 'alert alert-success';
        } elseif (version_compare($versions['Symfony']['value'], $version, '>')) {
            $versions['Symfony']['flag'] = 'alert alert-info';
            $versions['Symfony']['value'] .= $this->translator->trans('version.over', ['%required%' => $version], 'BusybeeHomeBundle');
        }

        $version = '2.4.3';
        if (version_compare($versions['Twig']['value'], $version, '<')) {
            $versions['Twig']['flag'] = 'alert alert-warning';
            $versions['Twig']['value'] .= $this->translator->trans('version.upgrade', ['%required%' => $version], 'BusybeeHomeBundle');
        } elseif (version_compare($versions['Twig']['value'], $version, '=')) {
            $versions['Twig']['flag'] = 'alert alert-success';
        } elseif (version_compare($versions['Twig']['value'], $version, '>')) {
            $versions['Twig']['flag'] = 'alert alert-info';
            $versions['Twig']['value'] .= $this->translator->trans('version.over', ['%required%' => $version], 'BusybeeHomeBundle');
        }

        $version = '2.5.3';
        if (version_compare($versions['Doctrine']['Common']['value'], $version, '<')) {
            $versions['Doctrine']['Common']['flag'] = 'alert alert-warning';
            $versions['Doctrine']['Common']['value'] .= $this->translator->trans('version.upgrade', ['%required%' => $version], 'BusybeeHomeBundle');
        } elseif (version_compare($versions['Doctrine']['Common']['value'], $version, '=')) {
            $versions['Doctrine']['Common']['flag'] = 'alert alert-success';
        } elseif (version_compare($versions['Doctrine']['Common']['value'], $version, '>')) {
            $versions['Doctrine']['Common']['flag'] = 'alert alert-info';
            $versions['Doctrine']['Common']['value'] .= $this->translator->trans('version.over', ['%required%' => $version], 'BusybeeHomeBundle');
        }

        $version = '2.5.12';
        if (version_compare($versions['Doctrine']['DBal']['value'], $version, '<')) {
            $versions['Doctrine']['DBal']['flag'] = 'alert alert-warning';
            $versions['Doctrine']['DBal']['value'] .= $this->translator->trans('version.upgrade', ['%required%' => $version], 'BusybeeHomeBundle');
        } elseif (version_compare($versions['Doctrine']['DBal']['value'], $version, '=')) {
            $versions['Doctrine']['DBal']['flag'] = 'alert alert-success';
        } elseif (version_compare($versions['Doctrine']['DBal']['value'], $version, '>')) {
            $versions['Doctrine']['DBal']['flag'] = 'alert alert-info';
            $versions['Doctrine']['DBal']['value'] .= $this->translator->trans('version.over', ['%required%' => $version], 'BusybeeHomeBundle');
        }

        $version = '2.5.4';
        if (version_compare($versions['Doctrine']['ORM']['value'], $version, '<')) {
            $versions['Doctrine']['ORM']['flag'] = 'alert alert-warning';
            $versions['Doctrine']['ORM']['value'] .= $this->translator->trans('version.upgrade', ['%required%' => $version], 'BusybeeHomeBundle');
        } elseif (version_compare($versions['Doctrine']['ORM']['value'], $version, '=')) {
            $versions['Doctrine']['ORM']['flag'] = 'alert alert-success';
        } elseif (version_compare($versions['Doctrine']['ORM']['value'], $version, '>')) {
            $versions['Doctrine']['ORM']['flag'] = 'alert alert-info';
            $versions['Doctrine']['ORM']['value'] .= $this->translator->trans('version.over', ['%required%' => $version], 'BusybeeHomeBundle');
        }


        foreach ($versions as $q => $w)
            if (is_array($w))
                ksort($versions[$q], SORT_STRING + SORT_FLAG_CASE);
        ksort($versions, SORT_STRING + SORT_FLAG_CASE);

        return $versions;
    }
}