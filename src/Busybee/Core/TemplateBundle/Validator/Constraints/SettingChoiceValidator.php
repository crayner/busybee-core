<?php

namespace Busybee\Core\TemplateBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Busybee\Core\SystemBundle\Setting\SettingManager;


class SettingChoiceValidator extends ChoiceValidator
{
	/**
	 * @var SettingManager
	 */
	private $sm;

	/**
	 * SettingValidator constructor.
	 *
	 * @param SettingManager $sm
	 */
	public function __construct(SettingManager $sm)
	{
		$this->sm = $sm;
	}

	/**
	 * @param mixed      $value
	 * @param Constraint $constraint
	 */
	public function validate($value, Constraint $constraint)
	{
		if (empty($value))
			return;

		$s = [];

		foreach ($this->sm->get($constraint->name) as $q => $w)
		{
			if (is_array($w))
				$s = array_merge($s, $w);
			else
				$s[$q] = $w;
		}

		$constraint->choices = array_merge($constraint->choices, $s);

		parent::validate($value, $constraint);
	}
}