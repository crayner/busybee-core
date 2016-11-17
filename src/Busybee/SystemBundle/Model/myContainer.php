<?php

namespace Busybee\SystemBundle\Model ;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class myContainer implements Container 
{
	/**
	 * @var	Container
	 */
	private $container ;
	
	/**
	 * Constuctor
	 */
	public function __construct(Container $container)
	{
		$this->container = $container ;
	}

    /**
     * Gets a parameter.
     *
     * @param string $name The parameter name
     *
     * @return mixed The parameter value
     *
     * @throws InvalidArgumentException if the parameter is not defined
     */
	public function getParameter($name)
	{
		if (false === (strpos($name, '.')))
        	return $this->container->getParameterBag()->get($name);
		$name = explode('.', $name);
		$value = $this->container->getParameterBag()->get($name[0]);
		array_shift($name);
		while (! empty($name))
		{
			$key = reset($name);
			$value = $value[$key];
			array_shift($name);
		}
		return $value ;
	}
	
	/**
     * Sets a service.
     *
     * @param string $id      The service identifier
     * @param object $service The service instance
     */
    public function set($id, $service)
	{
		return $this->container->set($id, $service);
	}

    /**
     * Gets a service.
     *
     * @param string $id              The service identifier
     * @param int    $invalidBehavior The behavior when the service does not exist
     *
     * @return object The associated service
     *
     * @throws ServiceCircularReferenceException When a circular reference is detected
     * @throws ServiceNotFoundException          When the service is not defined
     *
     * @see Reference
     */
    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
	{
		return $this->container->get($id, $invalidBehavior);
	}
	
    /**
     * Returns true if the given service is defined.
     *
     * @param string $id The service identifier
     *
     * @return bool true if the service is defined, false otherwise
     */
    public function has($id)
	{
		return $this->container->has($id);
	}

    /**
     * Check for whether or not a service has been initialized.
     *
     * @param string $id
     *
     * @return bool true if the service has been initialized, false otherwise
     */
    public function initialized($id)
	{
		return $this->container->initialized($id);
	}

    /**
     * Checks if a parameter exists.
     *
     * @param string $name The parameter name
     *
     * @return bool The presence of parameter in container
     */
    public function hasParameter($name)
	{
		return $this->container->hasParameter($name);
	}

    /**
     * Sets a parameter.
     *
     * @param string $name  The parameter name
     * @param mixed  $value The parameter value
     */
    public function setParameter($name, $value)
	{
		return $this->container->setParameter($name, $value);
	}
}
