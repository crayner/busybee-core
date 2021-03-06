<?php

namespace Busybee\Core\TemplateBundle\Validator\Constraints;

use Busybee\Facility\InstituteBundle\Entity\Department;
use Busybee\People\PersonBundle\Entity\Person;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueOrBlankValidator extends ConstraintValidator
{
	/**
	 * @var ObjectManager
	 */
	private $om;

	/**
	 * UniqueOrBlankValidator constructor.
	 *
	 * @param ObjectManager $sm
	 */
	public function __construct(ObjectManager $om)
	{
		$this->om = $om;
	}

	/**
	 * @param mixed      $value
	 * @param Constraint $constraint
	 */
	public function validate($value, Constraint $constraint)
	{
		if (empty($value))
		{
			$value = null;

			return;
		}

		$entity = $this->context->getObject();

		$where = 'p.' . $constraint->field . ' = :identifier';

		$result = $this->om->getRepository($constraint->data_class)->createQueryBuilder('p')
			->where($where)
			->andWhere('p.id != :id')
			->setParameter('identifier', $value)
			->setParameter('id', $entity->getId())
			->getQuery()
			->getResult();
		if (!empty($result))
		{
			$this->context->buildViolation($constraint->message)
				->setParameter('%string%', $value)
				->addViolation();
		}

		return;
	}
}