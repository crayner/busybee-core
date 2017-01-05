<?php
namespace Busybee\FormBundle\Validator ;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class BlankOrValid extends Constraint
{

    /**
     * BlankOrValid constructor.
     * @param null $options
     */
    public function __construct($options = null)
    {
        dump($options);

        if (is_null($options)) return ;

        if (is_array($options) && array_key_exists('groups', $options)) {
            throw new ConstraintDefinitionException(sprintf(
                'The option "groups" is not supported by the constraint %s',
                __CLASS__
            ));
        }

        parent::__construct($options);
    }

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'blank_or_valid_validator' ;
    }
}
