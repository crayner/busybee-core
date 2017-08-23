<?php

namespace Busybee\Core\SystemBundle\Model\Test;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Test Interface
 *
 * @version    15th November 2016
 * @since      15th November 2016
 * @author     Craig Rayner
 */
abstract class Tester implements TestInterface
{
	protected $container;


	/**
	 * Constructor
	 *
	 * @version    15th November 2016
	 * @since      15th November 2016
	 *
	 * @param    Symfony Container
	 *
	 * @return    this
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;

		return $this;
	}

	/**
	 * menu Item
	 *
	 * @version    15th November 2016
	 * @since      15th November 2016
	 *
	 * @param    integer $menu
	 *
	 * @return    boolean
	 */
	protected function menuItems($menu)
	{
		$items = $this->container->getParameter('items');
		foreach ($items as $item)
			if ($item['node'] == $menu)
				return false;

		return true;
	}
}