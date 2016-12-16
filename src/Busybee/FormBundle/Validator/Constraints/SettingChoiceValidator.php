<?php
namespace Busybee\FormBundle\Validator\Constraints ;

use Symfony\Component\Validator\Constraint ;
use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Busybee\SystemBundle\Setting\SettingManager ;


class SettingChoiceValidator extends ChoiceValidator
{
	private $sm;

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (empty($value))
            return ;

        $constraint->choices = $this->sm->get($constraint->name);

		parent::validate($value, $constraint);
    }

    /**
     * SettingValidator constructor.
     * @param SettingManager $sm
     */
    public function __construct(SettingManager $sm)
    {
        $this->sm = $sm ;
    }
}