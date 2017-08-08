<?php
namespace Busybee\HomeBundle\Model ;

use Symfony\Component\DependencyInjection\ContainerInterface as Container ;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class MenuManager
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var \Busybee\SecurityBundle\Model\PageManager|object
     */
    protected $pageManager;

    /**
     * @var object|\Symfony\Component\Security\Core\Authorization\AuthorizationChecker
     */
    private $checker;

    /**
     * @var array
     */
    private $pageRoles;

    /**
     * MenuManager constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
	{
		$this->container = $container;

        $this->pageManager = $this->container->get('page.manager');

        $this->checker = $container->get('security.authorization_checker');

        $x = $container->get('security.page.repository')->findAll();

        $this->pageRoles = [];
        foreach ($x as $page) {
            $this->pageRoles[$page->getRoute()] = $page->getRoles();
        }

		return $this ;
	}

    /**
     * @return mixed
     */
    public function getMenu()
	{
		$nodes = $this->container->getParameter('nodes');
		$nodes = $this->msort($nodes, 'order');
        foreach ($nodes as $q => $node) {
            $items = $this->getMenuItems($node['menu']);
            if (empty($items))
                unset($nodes[$q]);
        }
		return $nodes;
	}

    /**
     * Array sort for multidimensional arrays
     * @param $array
     * @return mixed
     */
    private function msort($array)
	{
        usort($array, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        }
        );
		return $array ;
	}

    /**
     * @param $node
     * @return mixed
     */
    public function getMenuItems($node)
	{
		$items = $this->container->getParameter('items');
        $result = [];
        foreach ($items as $w)
            if ($w['node'] == $node && $this->itemRoleCheck($w))
			{
                $w['parameters'] = ! empty($w['parameters']) ? $w['parameters'] : array() ;
                if (isset($w['route']))
                    $w['role'] = $this->getRouteAccess($w['route'], empty($w['role']) ? null : $w['role']);
                if (empty($w['role']))
                    unset($w['role']);
				$result[] = $w;
			}
		$items = $this->msort($result, 'order');
        return $items;
	}

    /**
     * get Route Access
     *
     * @param string $route
     * @param string $role
     * @return array
     */
    private function getRouteAccess($route, $role)
    {
        $roles = [];
        $page = $this->pageManager->findOneByRoute($route, $role);
        if (! empty($page))
            $roles = $page->getRoles();
        if (in_array(null, $roles))
            $roles = null;
        return $roles;
    }

    /**
     * test Menu Item
     *
     * @param string $test
     * @return bool
     * @throws InvalidArgumentException
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
                throw new InvalidArgumentException('Do not know how to deal with ' . $test['comparitor'] . ' in ' . __FILE__);
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

    /**
     * @param   array $node
     * @return  bool
     */
    private function itemRoleCheck($node)
    {
        if (null === $this->checker)
            return false;

        if (empty($node['role']) && empty($node['route']))
            return true;

        if (empty($node['role']) && empty($this->pageRoles[$node['route']]))
            return true;

        if (!empty($node['route'])) {

            if (empty($this->pageRoles[$node['route']]) || (count($this->pageRoles[$node['route']]) == 1 && is_null($this->pageRoles[$node['route']][0])))
                $this->pageRoles[$node['route']] = [];

            if (!empty($node['role']))
                $this->pageRoles[$node['route']] = array_merge($this->pageRoles[$node['route']], [$node['role']]);

            if (empty($this->pageRoles[$node['route']]))
                return true;

            foreach ($this->pageRoles[$node['route']] as $role) {
                try {
                    if ($this->checker->isGranted($role))
                        return true;
                } catch (AuthenticationCredentialsNotFoundException $e) {
                    // Do Nothin!
                }
            }
            return false;
        } else {
            if (empty($node['role']))
                return true;

            $role = $node['role'];
            try {
                return $this->checker->isGranted($role);
            } catch (AuthenticationCredentialsNotFoundException $e) {
                return false;
            }
        }
        return false;
    }
}