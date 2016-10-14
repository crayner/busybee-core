<?php
namespace General\ValidationBundle\Model ;

use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase ;

abstract class ConstraintValidator extends ConstraintValidatorBase {

	protected $repository ;
	protected $params ;
	
	public function __construct( \General\ValidationBundle\Entity\ValidatorRepository $repository)
	{
		
		$this->repository		= $repository ;
		$this->defaultParams();
	}

	protected function loadParams($name)
	{
	
		$x = $this->repository->findOneBy(array('ConstraintName' => $name));
		if (! empty($x)) {
			$this->params 			= $x;
			$this->params->loaded 	= true;
		}
		return $this->params ;
	}

	protected function defaultParams()
	{
	
		$this->params				= new \stdClass();
		$this->params->loaded 		= false;
		$this->params->match		= '';
		$this->params->replace		= '';
		$this->params->blank		= true;
		$this->params->unique		= false;
		$this->params->message		= '';
		$this->params->format		= '';
		$this->params->length		= null;
		$this->params->minlength	= 0;
		$this->params->maxlength	= null;
		return $this->params;
	}
}