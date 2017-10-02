<?php

namespace Busybee\People\LocalityBundle\Validator\Constraints;

use Busybee\Core\SystemBundle\Setting\SettingManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class LocalityValidator extends ConstraintValidator
{
	/**
	 * @var SettingManager
	 */
	private $sm;

	/**
	 * LocalityValidator constructor.
	 *
	 * @param SettingManager $sm
	 */
	public function __construct(SettingManager $sm)
	{
		$this->sm = $sm;
	}

	/**
	 * @param object     $entity
	 * @param Constraint $constraint
	 */
	public function validate($entity, Constraint $constraint)
	{

		if (empty($entity))
			return;

		$pattern = $this->sm->get('locality.validation');

		if (is_null($pattern))
			return;

		foreach ($pattern as $name => $field)
		{
			dump([$name, $field]);
		}

		return;

		if (preg_match($pattern, $value) !== 1)
		{
			$this->context->buildViolation($constraint->message)
				->addViolation();
		}
	}
}