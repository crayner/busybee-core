<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Model;

/**
 * Client Model
 */
class ClientModel
{
	public function __construct()
	{
		$this->setDisability('00');
		$this->setPriorEducation('000');
		$this->setEnglishProficiency('@');
		$this->setIndigenous('@');
		$this->setLanguage('@@@@');
	}
}