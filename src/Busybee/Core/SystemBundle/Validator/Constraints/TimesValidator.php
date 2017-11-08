<?php

namespace Busybee\Core\SystemBundle\Validator\Constraints;

use Busybee\Core\SystemBundle\Model\DaysTimesManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TimesValidator extends ConstraintValidator
{
	/**
	 * @var DaysTimesManager
	 */
	private $manager;

	/**
	 * HousesValidator constructor.
	 *
	 * @param DaysTimesManager $manager
	 */
	public function __construct(DaysTimesManager $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * @param mixed      $value
	 * @param Constraint $constraint
	 */
	public function validate($value, Constraint $constraint)
	{
		if ($value->getOpen() > $value->getBegin())
			$this->context->buildViolation('school.admin.day_time.open.error')
				->atPath('open')
				->addViolation();
		if ($value->getBegin() > $value->getFinish())
			$this->context->buildViolation('school.admin.day_time.begin.error')
				->atPath('begin')
				->addViolation();
		if ($value->getFinish() > $value->getClose())
			$this->context->buildViolation('school.admin.day_time.finish.error')
				->atPath('finish')
				->addViolation();
	}

}