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

		$test   = [];
		$tutors = [];
		foreach ($value as $q => $group)
		{
			$test[$group->getNameShort()] = isset($test[$group->getNameShort()]) ? $test[$group->getNameShort()] + 1 : 1;

			if ($test[$group->getNameShort()] > 1)
				$this->context->buildViolation('calendar.group.nameshort.unique', ['%grade%' => $group->getNameShort()])
					->atPath('[' . $q . '].nameShort')
					->addViolation();

			if (!is_null($group->getYearTutor()))
			{
				$tutors[$group->getYearTutor()->getId()] = empty($tutors[$group->getYearTutor()->getId()]) ? 1 : $tutors[$group->getYearTutor()->getId()] + 1;
				if ($tutors[$group->getYearTutor()->getId()] > 1)
					$this->context->buildViolation('calendar.group.yeartutor.unique', ['%{name}' => $group->getYearTutor()->formatName()])
						->atPath('[' . $q . '].yearTutor')
						->addViolation();
			}
		}
	}
}