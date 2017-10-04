<?php

namespace Busybee\People\PersonBundle\Entity;


class FieldMatch
{
	/**
	 * @var
	 */
	private $source;

	/**
	 * @var
	 */
	private $destination;

	/**
	 * @return mixed
	 */
	public function getSource()
	{
		return $this->source;
	}

	/**
	 * @param mixed $source
	 */
	public function setSource($source): FieldMatch
	{
		$this->source = $source;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDestination()
	{
		return $this->destination;
	}

	/**
	 * @param mixed $destination
	 */
	public function setDestination($destination): FieldMatch
	{
		$this->destination = $destination;

		return $this;
	}
}