<?php

namespace Busybee\Core\SystemBundle\Model;

class Bundle
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var boolean
	 */
	private $active;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $namespace;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var array
	 */
	private $requirements;

	/**
	 * @var array
	 */
	private $exclusions;

	/**
	 * @var boolean
	 */
	private $route;

	/**
	 * @var string
	 */
	private $routeResource;

	/**
	 * @var string
	 */
	private $routeType;

	/**
	 * @var string
	 */
	private $routePrefix;

	public function __construct($name, $bundle)
	{
		$this->setName($name)
			->setActive(!empty($bundle['active']) && $bundle['active'] ? true : false)//default to false
			->setNamespace($bundle['namespace'])
			->setRoute(isset($bundle['route']) ? true : false)
			->setDescription(isset($bundle['description']) ? $bundle['description'] : '')
			->setRequirements(isset($bundle['requirements']) ? $bundle['requirements'] : [])
			->setExclusions(isset($bundle['exclusions']) ? $bundle['exclusions'] : [])
			->setType(isset($bundle['type']) && strtolower($bundle['type']) === 'core' ? 'core' : 'plugin'); // default to plugin
		if ($this->isRoute())
		{
			$this->setRoutePrefix(isset($bundle['route']['prefix']) ? $bundle['route']['prefix'] : $this->getName())//default to name string
			->setRouteResource($bundle['route']['resource'])
				->setRouteType(isset($bundle['route']['type']) ? $bundle['route']['type'] : 'yaml'); //default to yaml
		}
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		if (!isset($this->active))
			$this->setActive(false);
		if ($this->getType() === 'core')
			$this->setActive(true);

		return $this->active;
	}

	/**
	 * @param bool $active
	 */
	public function setActive(bool $active)
	{
		$this->active = $active;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isCore(): bool
	{
		if ($this->getType() === 'core')
			return true;

		return false;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType(string $type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getNamespace(): string
	{
		return $this->namespace;
	}

	/**
	 * @param string $namespace
	 */
	public function setNamespace(string $namespace)
	{
		$this->namespace = $namespace;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getRouteResource(): string
	{
		if (!$this->isRoute() || empty($this->routeResource))
			$this->setRouteResource('');

		return $this->routeResource;
	}

	/**
	 * @param string $routeResource
	 */
	public function setRouteResource(string $routeResource = null)
	{
		$this->routeResource = $routeResource;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isRoute(): bool
	{
		return $this->route;
	}

	/**
	 * @param bool $route
	 */
	public function setRoute(bool $route)
	{
		$this->route = $route;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getRouteType(): string
	{
		if (!$this->isRoute() || empty($this->routeType))
			$this->setRouteType('');

		return $this->routeType;
	}

	/**
	 * @param string $routeType
	 */
	public function setRouteType(string $routeType = null)
	{
		if (!in_array(strtolower($routeType), ['yaml', 'xml', 'php']))
			$routeType = 'yaml';
		$this->routeType = strtolower($routeType);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getRoutePrefix(): string
	{
		if (!$this->isRoute() || empty($this->routePrefix))
			$this->setRoutePrefix('');

		return $this->routePrefix;
	}

	/**
	 * @param string $routePrefix
	 */
	public function setRoutePrefix(string $routePrefix = null)
	{
		$this->routePrefix = $routePrefix;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		if (empty($this->description))
			return 'bundle.description.missing';

		return $this->description;
	}

	/**
	 * @param string $description
	 *
	 * @return Bundle
	 */
	public function setDescription(string $description = null)
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getRequirements(): array
	{
		return $this->requirements;
	}

	/**
	 * @param array $requirements
	 */
	public function setRequirements(array $requirements): Bundle
	{
		$this->requirements = $requirements;

		return $this;
	}
	/**
	 * @return array
	 */
	public function getExclusions(): array
	{
		return $this->exclusions;
	}

	/**
	 * @param array $exclusions
	 */
	public function setExclusions(array $exclusions): Bundle
	{
		$this->exclusions = $exclusions;

		return $this;
	}
}