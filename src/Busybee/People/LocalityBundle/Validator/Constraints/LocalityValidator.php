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

		$validator = $this->context->getValidator();

		foreach ($pattern as $name => $constraints)
		{
			if (!empty($constraints))
			{
				$validators = [];
				$getName    = 'get' . ucfirst($name);
				foreach ($constraints as $constraint => $options)
				{
					if (empty($options))
						$options = [];

					$cName        = "Symfony\\Component\\Validator\\Constraints\\" . $constraint;
					$validators[] = new $cName($options);

				}
				$errors = $validator->startContext()->atPath($name)->validate(
					$entity->$getName(),
					$validators,
					['Default']
				)
					->getViolations();

				foreach ($errors as $error)
					$this->context->buildViolation($error->getMessage())
						->atPath($name)
						->addViolation();

			}

		}

		return;
	}
}