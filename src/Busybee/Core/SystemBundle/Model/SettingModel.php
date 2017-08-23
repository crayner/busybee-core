<?php

namespace Busybee\Core\SystemBundle\Model;

class SettingModel
{
	public function __construct()
	{
		$this->setCreatedBy(null);
		$this->setModifiedBy(null);
	}

	public function getNameSelect()
	{
		return $this->getId();
	}
}