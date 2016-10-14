<?php

namespace Busybee\DatabaseBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Field
 */
class File
{
	private $name ;
	
	private $refresh ;
	
	public function getName()
	{
		$this->name;
	}
	
	public function setName( UploadedFile $name)
	{
		$this->name = $name;
	}
	
	public function getRefresh()
	{
		$this->refresh;
	}
	
	public function setRefresh($refresh)
	{
		$this->refresh = (bool)$refresh;
	}
}
