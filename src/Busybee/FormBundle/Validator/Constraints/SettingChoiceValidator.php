<?php
namespace Busybee\FormBundle\Validator\Constraints ;

use Symfony\Component\Validator\Constraint ;
use Symfony\Component\Validator\Constraints\ChoiceValidator;
use Busybee\SystemBundle\Setting\SettingManager ;


class SettingChoiceValidator extends ChoiceValidator
{
    /**
     * @var SettingManager
     */
    private $sm;

    /**
     * SettingValidator constructor.
     * @param SettingManager $sm
     */
    public function __construct(SettingManager $sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (empty($value))
            return ;

        $constraint->choices = array_merge($constraint->choices, $this->sm->get($constraint->name));

		parent::validate($value, $constraint);
    }
}