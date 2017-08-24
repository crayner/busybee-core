<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Form\DataTransformer;

use Busybee\CurriculumBundle\Entity\Subject;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;


class SubjectTransformer implements DataTransformerInterface
{
	private $manager;

	public function __construct(ObjectManager $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * Transform an Object to a string
	 *
	 * @param  object $data Subject
	 *
	 * @return string
	 */
	public function transform($data)
	{
		if ($data instanceof Subject)
			return $data->getId();
		if (intval($data) == $data)
			return $data;

		return null;
	}

	/**
	 * Transforms a string to a Subject Object
	 *
	 * @param  string $data
	 *
	 * @return Object
	 */
	public function reverseTransform($data)
	{
		if (is_null($data))
			return null;
		$subject = $this->manager
			->getRepository('BusybeeCurriculumBundle:Subject')
			// query for the issue with this id
			->find($data);

		if (is_null($subject))
		{
			// causes a validation error
			// this message is not shown to the user
			// see the invalid_message option
			throw new TransformationFailedException('This message is 0verwritten by the validation message. ' . __FILE__ . __LINE__);
		}

		return $subject;
	}
}