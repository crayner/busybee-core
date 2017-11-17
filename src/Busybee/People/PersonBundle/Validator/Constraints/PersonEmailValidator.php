<?php

namespace Busybee\People\PersonBundle\Validator\Constraints;

use Busybee\People\PersonBundle\Entity\Person;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class PersonEmailValidator extends ConstraintValidator
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
		if (empty($value))
			return;

		$object = $this->context->getObject();

		if ($constraint->errorPath == 'email' || $constraint->errorPath == 'email2')
		{

			$result = $this->em->getRepository(Person::class)->createQueryBuilder('p')
				->select('p.id')
				->where('(p.email = :email1 OR p.email2 = :email2)')
				->andWhere('p.id <> :id')
				->setParameter('email1', $value)
				->setParameter('email2', $value)
				->setParameter('id', $object->getId())
				->getQuery()
				->getResult();
			if (!empty($result))
			{
				$this->context->buildViolation($constraint->message)
					->setParameter('%string%', $value)
					->addViolation();
			}
		}
	}
}