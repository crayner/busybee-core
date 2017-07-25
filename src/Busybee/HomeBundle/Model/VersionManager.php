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
     * @var bool
     */
    private $ok = true;

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
    public function getVersion($install = false)
    {
        $versions = [];


        $versions['Twig'] = \Twig_Environment::VERSION;
        $versions['Symfony'] = Kernel::VERSION;
        $versions['Doctrine']['ORM'] = Version::VERSION;
        $versions['Doctrine']['Common'] = \Doctrine\Common\Version::VERSION;
        $versions['Database']['Server'] = $this->connection->getWrappedConnection()->getServerVersion();
        $versions['Database']['Driver'] = $this->connection->getParams()['driver'];
        $versions['Database']['Character Set'] = $this->connection->getParams()['charset'];
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

        if (!$install) {
            $versions['Busybee']['System'] = $this->settingManager->get('version.system');
            $versions['Busybee']['Database'] = $this->settingManager->get('version.database');

            if ($versions['Busybee']['System'] !== $this->version['system']) {
                $versions['Busybee']['System'] = [];
                $versions['Busybee']['System']['flag'] = 'alert alert-warning';
                $versions['Busybee']['System']['value'] = $this->translator->trans('software.required', ['%required%' => $this->version['system']], 'BusybeeHomeBundle');
            } else {
                $versions['Busybee']['System'] = [];
                $versions['Busybee']['System']['flag'] = 'alert alert-success';
                $versions['Busybee']['System']['value'] = $this->translator->trans('version.equal', ['%required%' => $this->version['system']], 'BusybeeHomeBundle');
            }
            if ($versions['Busybee']['Database'] !== $this->version['database']) {
                $versions['Busybee']['Database'] = [];
                $versions['Busybee']['Database']['flag'] = 'alert alert-warning';
                $versions['Busybee']['Database']['value'] = $this->translator->trans('software.required', ['%required%' => $this->version['database']], 'BusybeeHomeBundle');
            } else {
                $versions['Busybee']['Database'] = [];
                $versions['Busybee']['Database']['flag'] = 'alert alert-success';
                $versions['Busybee']['Database']['value'] = $this->translator->trans('version.equal', ['%required%' => $this->version['database']], 'BusybeeHomeBundle');
            }
        }

        $phpVersions = [];
        $phpVersions['Core']['low'] = '7.1.0';
        $phpVersions['Core']['high'] = '7.1.9';
        $phpVersions['Core']['string'] = '7.1.0 - 7.1.9';
        $phpVersions['apcu'] = '5.1.8';
        $phpVersions['intl'] = '1.1.0';
        $phpVersions['json'] = '1.5.0';

        foreach ($phpVersions as $name => $version)
            if (!is_array($version)) {
                $phpVersions[$name] = [];
                $phpVersions[$name]['low'] = $version;
                $phpVersions[$name]['high'] = $version;
                $phpVersions[$name]['string'] = $version;
            }

        foreach ($phpVersions as $name => $version) {
            if (!isset($versions['PHP'][$name])) {
                $versions['PHP'][$name]['value'] = $this->translator->trans('software.required', ['%required%' => $version['string']], 'BusybeeHomeBundle');
                $versions['PHP'][$name]['flag'] = 'alert alert-danger';
            } else
                $versions['PHP'][$name] = $this->fullCompare($versions['PHP'][$name], $version);
        }
        foreach ($versions['PHP'] as $name => $w)
            if (!isset($phpVersions[$name]))
                unset($versions['PHP'][$name]);

        $version['low'] = '5.6.30';
        $version['high'] = '5.6.39';
        $version['string'] = '5.6.30 - 5.6.39';
        $versions['Database']['Server'] = $this->fullCompare($versions['Database']['Server'], $version);

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

        $version = [];
        $version['string'] = '3.3 - 3.4';
        $version['low'] = '3.3';
        $version['high'] = '3.4';
        $versions['Symfony'] = $this->fullCompare($versions['Symfony'], $version);

        $version['string'] = '2.4.0 - 2.4.9';
        $version['low'] = '2.4.0';
        $version['high'] = '2.4.9';
        $versions['Twig'] = $this->fullCompare($versions['Twig'], $version);

        $version['string'] = '2.7.3';
        $version['low'] = '2.7.3';
        $version['high'] = '2.7.3';
        $versions['Doctrine']['Common'] = $this->fullCompare($versions['Doctrine']['Common'], $version);

        $version['string'] = '2.5.12 - 2.5.13';
        $version['low'] = '2.5.12';
        $version['high'] = '2.5.113';
        $versions['Doctrine']['DBal'] = $this->fullCompare($versions['Doctrine']['DBal'], $version);

        $version['string'] = '2.5.5-DEV';
        $version['low'] = '2.5.5-DEV';
        $version['high'] = '2.5.6';
        $versions['Doctrine']['ORM'] = $this->fullCompare($versions['Doctrine']['ORM'], $version);


        foreach ($versions as $q => $w)
            if (is_array($w))
                ksort($versions[$q], SORT_STRING + SORT_FLAG_CASE);
        ksort($versions, SORT_STRING + SORT_FLAG_CASE);

        return $versions;
    }

    /**
     * Full Compare
     *
     * @param iterable $test
     * @param iterable $standard
     * @return iterable
     */
    private function fullCompare(iterable $test, iterable $standard): iterable
    {
        if (version_compare($test['value'], $standard['low'], '<')) {
            $test['flag'] = 'alert alert-warning';
            $test['value'] .= $this->translator->trans('version.upgrade', ['%required%' => $standard['string']], 'BusybeeHomeBundle');
        } elseif (version_compare($test['value'], $standard['low'], '>=') && version_compare($test['value'], $standard['high'], '<=')) {
            $test['flag'] = 'alert alert-success';
            $test['value'] .= $this->translator->trans('version.equal', ['%required%' => $standard['string']], 'BusybeeHomeBundle');
        } elseif (version_compare($test['value'], $standard['high'], '>')) {
            $test['flag'] = 'alert alert-info';
            $test['value'] .= $this->translator->trans('version.over', ['%required%' => $standard['string']], 'BusybeeHomeBundle');
        }
        return $test;
    }
}