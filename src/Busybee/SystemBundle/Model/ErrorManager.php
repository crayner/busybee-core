<?php

namespace Busybee\SystemBundle\Model ;

use Doctrine\Common\Collections\ArrayCollection ;
use stdClass ;

class ErrorManager 
{
	private $errors;
	
	public function __construct()
	{
		$this->errors = new ArrayCollection();
	}

    /**
     * Add Error
     *
     * @param $error
     *
     * @return Person
     */
    public function addError($error)
    {
        $this->errors->add($error);

        return $this;
    }

    /**
     * Remove Error
     *
     * @param $error
	 */
    public function removeError($error)
    {
        $this->errors->removeElement($error);
    }

    /**
     * Get phone
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * create Error
     *
     * @param	string	$source
     * @param	string	$item
     * @param	string	$message
     * @param	mixed	$value
     * @param	stdClass	$route
     *
     * @return 	stdClass
     */
    public function createError($source, $item, $message, $value, $route)
    {
		$error = new stdClass() ;
		$error->key = md5(uniqid());
		$error->source = $source ;
		$error->item = $item ;
		$error->message = $message ;
		$error->value = $value;
		if (is_array($value)) $error->value = json_encode($value);
		if (is_object($value)) $error->value = json_encode($value);
		$error->route = $route;
        return $error;
    }
}
