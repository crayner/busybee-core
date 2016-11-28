<?php
namespace Busybee\HomeBundle\Model ;

use Symfony\Component\DependencyInjection\ContainerInterface as Container ;

class MenuManager
{
	protected $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
		
		return $this ;
	}
	
	public function getMenu()
	{
		$nodes = $this->container->getParameter('nodes');
		$nodes = $this->msort($nodes, 'order');
		return $nodes;
	}

	//Array sort for multidimensional arrays
	private function msort($array)
	{
		if (phpversion() < '5.6.00') throw new \Exception('You must be using a version of PHP >= 5.6');

		if (phpversion() < '7.0.00')
			usort($array, function($a, $b) {
					return $a['order'] - $b['order'];
				}
			);
		else
			usort($array, function($a, $b) {
					return $a['order'] <=> $b['order'];
				}
			);
		return $array ;
	}

	public function getMenuItems($node)
	{
		$items = $this->container->getParameter('items');
		$result = array();
		foreach( $items as $w)
			if ($w['node'] == $node)
			{
				if (empty($w['parameters'])) $w['parameters'] = array();
				$result[] = $w;
			}
		$items = $this->msort($result, 'order');
		return $items;
	}

	/**
	 * @return	boolean
	 */
	public function testMenuItem($test)
	{
		$test['default1'] = isset($test['default1']) ? $test['default1'] : null ;
		$test['default2'] = isset($test['default2']) ? $test['default2'] : null ;
		$value1 = $this->manageValue($test['value1'], $test['default1']);
		$value2 = $this->manageValue($test['value2'], $test['default2']);

		$test['comparitor'] = empty($test['comparitor']) ? '=' : $test['comparitor'] ;
		switch($test['comparitor'])
		{
			case '==':
				if ($value1 == $value2) return true ;
				break;
			case '!=':
				if ($value1 != $value2) return true ;
				break;
			case '<':
				if ($value1 < $value2) return true ;
				break;
			default:
				throw new \Exception('Do not know how to deal with '. $test['comparitor'] . ' in '.__FILE__);
		}
		return false;
	}

	/**
	 * @return	mixed
	 */
	private function manageValue($value, $default = null)
	{
		if (0 === strpos($value, 'setting.'))
			return $this->container->get('setting.manager')->get(substr($value, 8), $default);

		if (0 === strpos($value, 'parameter.'))
		{
			$name = substr($value, 10);
			if (strpos($name, '.') === false)
				return $this->container->getParameter($name);
			$name = explode('.', $name);
			$value = $this->container->getParameter($name[0]);
			array_shift($name);
			while (! empty($name))
			{
				$key = reset($name);
				$value = $value[$key];
				array_shift($name);
			}
			return $value;
		}
		
		if (0 === strpos($value, 'test.'))
			return $this->container->get($value)->test();
		
		return $value ;
	}

	/**
	 * @return	boolean
	 */
	public function menuRequired($menu)
	{
		$items = $this->container->getParameter('items');
		foreach ($items as $item)
			if (intval($menu) == intval($item['node']))
				return true;
		return false;
	}
}