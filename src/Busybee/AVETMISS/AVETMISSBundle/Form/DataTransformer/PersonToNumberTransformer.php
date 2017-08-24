<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Form\DataTransformer;

use Busybee\PersonBundle\Entity\Person;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PersonToNumberTransformer implements DataTransformerInterface
{
	private $manager;

	public function __construct(ObjectManager $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * Transforms an object (person) to a string (number).
	 *
	 * @param  Person|null $person
	 *
	 * @return string
	 */
	public function transform($person)
	{
		if ($person instanceof Person)
			return $person->getId();

		return '';
	}

	/**
	 * Transforms a string (number) to an object (person).
	 *
	 * @param  string $personNumber
	 *
	 * @return Person|null
	 * @throws TransformationFailedException if object (Person) is not found.
	 */
	public function reverseTransform($personNumber)
	{
		// no issue number? It's optional, so that's ok
		if (empty($personNumber))
		{
			return null;
		}

		$person = $this->manager
			->getRepository('BusybeePersonBundle:Person')
			// query for the issue with this id
			->findOneById($personNumber);

		if (is_null($person))
		{
			// causes a validation error
			// this message is not shown to the user
			// see the invalid_message option
			throw new TransformationFailedException('This message is averwritten by the validation message. ' . __FILE__ . __LINE__);
		}

		return $person;
	}
}