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
	private function msort($array, $id = 'id', $sort_ascending = true)
	{
		$temp_array = array();
		while (count($array) > 0) {
			$lowest_id = 0;
			$index = 0;
			foreach ($array as $item) {
				if (isset($item[$id])) {
					if ($array[$lowest_id][$id]) {
						if (strtolower($item[$id]) < strtolower($array[$lowest_id][$id])) {
							$lowest_id = $index;
						}
					}
				}
				++$index;
			}
			$temp_array[] = $array[$lowest_id];
			$array = array_merge(array_slice($array, 0, $lowest_id), array_slice($array, $lowest_id + 1));
		}
		if ($sort_ascending) {
			return $temp_array;
		} else {
			return array_reverse($temp_array);
		}
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
}