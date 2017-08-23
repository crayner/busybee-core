<?php

namespace Busybee\Core\SystemBundle\Model;

use stdClass;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\Tools\SchemaTool;
use Busybee\Core\SystemBundle\Update\UpdateInterface;

/**
 * Update Manager
 *
 * @version    15th November 2016
 * @since      15th November 2016
 * @author     Craig Rayner
 */
abstract class PluginManager implements UpdateInterface
{
	/**
	 * @var    Container
	 */
	protected $container;

	/**
	 * @var    Setting Manager
	 */
	protected $sm;

	/**
	 * @var    Entity Manager
	 */
	protected $em;


	/**
	 * Constructor
	 *
	 * @version    15th November 2016
	 * @since      23rd October 2016
	 *
	 * @param    Symfony Container
	 *
	 * @return    this
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
		$this->sm        = $this->container->get('setting.manager');
		$this->sm->setCurrentUser($this->container->get('security.token_storage')->getToken()->getUser());
		$this->em = $container->get('doctrine')->getManager();

		return $this;
	}

	/**
	 * get Count
	 *
	 * @version    23rd October 2016
	 * @since      23rd October 2016
	 * @return    integer
	 */
	public function getCount()
	{
		return $this->count;
	}

	/**
	 * increment Version
	 *
	 * @version    20th October 2016
	 * @since      20th October 2016
	 *
	 * @param    string $version
	 *
	 * @return    string Version
	 */
	protected function incrementVersion($version)
	{
		$v = explode('.', $version);
		if (!isset($v[2])) $v[2] = 0;
		if (!isset($v[1])) $v[1] = 0;
		if (!isset($v[0])) $v[0] = 0;
		while (count($v) > 3)
			array_pop($v);
		$v[2]++;
		if ($v[2] > 99)
		{
			$v[2] = 0;
			$v[1]++;
		}
		if ($v[1] > 9)
		{
			$v[1] = 0;
			$v[0]++;
		}
		$v[2] = str_pad($v[2], 2, '00', STR_PAD_LEFT);

		return implode('.', $v);
	}
}