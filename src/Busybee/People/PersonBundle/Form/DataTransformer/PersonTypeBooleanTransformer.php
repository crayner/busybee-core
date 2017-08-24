<?php

namespace Busybee\People\PersonBundle\Form\DataTransformer;

use Busybee\People\PersonBundle\Entity\Person;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;

class PersonTypeBooleanTransformer implements DataTransformerInterface
{
	/**
	 * @var ObjectManager
	 */
	private $manager;

	/**
	 * @var string
	 */
	private $entityClass;

	/**
	 * @var Person
	 */
	private $person;

	/**
	 * PersonTypeBooleanTransformer constructor.
	 *
	 * @param ObjectManager $manager
	 * @param               $entityClass
	 */
	public function __construct(ObjectManager $manager, $entityClass, Person $person)
	{
		$this->manager     = $manager;
		$this->entityClass = $entityClass;
		$this->person      = $person;
	}

	/**
	 * Transform an Object to a boolean
	 *
	 * @param  object $data
	 *
	 * @return boolean
	 */
	public function transform($data)
	{
		if ($data instanceof $this->entityClass)
			$data = '1';
		else
			$data = '0';

		return $data;
	}

	/**
	 * @param mixed $data
	 *
	 * @return mixed
	 */
	public function reverseTransform($data)
	{
		if (empty($data) || false === boolval($data))
			return null;
		$entity = null;
		if (boolval($data))
		{
			if ($this->person->getId() > 0)
			{
				$entity = $this->manager->getRepository($this->entityClass)->findOneByPerson($this->person->getId());
			}
			else
				$entity = new $this->entityClass();
		}

		return $entity;
	}
}