<?php

namespace Busybee\People\PersonBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class PersonNameValidator extends ConstraintValidator
{
	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * PersonEmailValidator constructor.
	 *
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	/**
	 * @param mixed      $value
	 * @param Constraint $constraint
	 */
	public function validate($value, Constraint $constraint)
	{
		if (empty($value->getPreferredName()))
			$value->setPreferredName($value->getFirstName());

		if (empty($value->getOfficialName()))
		{
			$value->setOfficialName($value->getFirstName() . ' ' . $value->getSurname());
		}
	}
}