<?php

namespace Busybee\Core\FormBundle\Validator;

use Symfony\Component\Validator\Constraints\Image;

class Logo extends Image
{
	public $mimeTypes = array('image/png', 'image/gif');
	public $minWidth = 250;
	public $maxSize = '750k';
	public $allowSquare = true;
	public $allowLandscape = true;
	public $allowPortrait = false;
	public $detectCorrupted = true;

	public function validatedBy()
	{
		return 'logo_validator';
	}
}
