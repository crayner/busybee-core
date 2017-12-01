<?php

namespace Busybee\Core\CalendarBundle\Validator\Constraints;

use Busybee\Core\CalendarBundle\Entity\CalendarGroup;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase;

class CalendarGroupValidator extends ConstraintValidatorBase
{
	/**
	 * @var ObjectManager
	 */
	private $om;

	/**
	 * GradeValidator constructor.
	 *
	 * @param ObjectManager $objectManager
	 */
	public function __construct(ObjectManager $objectManager)
	{
		$this->om = $objectManager;
	}

	/**
	 * @param mixed      $value
	 * @param Constraint $constraint
	 */
	public function validate($value, Constraint $constraint)
	{
		if (empty($value))
			return;

		$years = $this->om->getRepository(CalendarGroup::class)->findBy(['year' => $constraint->year->getId()], ['sequence' => 'ASC']);

		if (!empty($years))
			foreach ($years as $y)
			{
				if (!$value->contains($y))
					if (!$y->canDelete())
					{
						$this->context->buildViolation('calendar.group.error.delete', ['%grade%' => $y->getFullName()])
							->addViolation();

						return;
					}
			}

		$test = [];
		foreach ($value as $group)
			$test[$group->getNameShort()] = isset($test[$group->getNameShort()]) ? $test[$group->getNameShort()] + 1 : 1;

		foreach ($test as $y => $w)
			if ($w > 1)
			{
				$this->context->buildViolation($constraint->message, ['%grade%' => $y])
					->addViolation();

				return;

			}
	}
}