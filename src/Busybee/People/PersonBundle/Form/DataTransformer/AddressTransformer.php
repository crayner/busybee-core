<?php

namespace Busybee\People\PersonBundle\Form\DataTransformer;

use Busybee\People\PersonBundle\Entity\Address;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;


class AddressTransformer implements DataTransformerInterface
{
	private $manager;

	public function __construct(ObjectManager $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * Transform an Object to a string
	 *
	 * @param  object $data Address
	 *
	 * @return string
	 */
	public function transform($data)
	{
		if ($data instanceof Address)
			return $data->getId();

		return null;
	}

	/**
	 * @param mixed $data
	 *
	 * @return Address|null|object
	 */
	public function reverseTransform($data)
	{
		if (empty($data))
			return null;
		$address = $this->manager
			->getRepository('BusybeePersonBundle:Address')
			// query for the issue with this id
			->find($data);

		if (is_null($address))
		{
			// causes a validation error
			// this message is not shown to the user
			// see the invalid_message option
			throw new TransformationFailedException('This message is over written by the validation message. ' . __FILE__ . __LINE__);
		}

		return $address;
	}
}