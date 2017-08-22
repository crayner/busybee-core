<?php

namespace Busybee\Core\FormBundle\Validator;

use Symfony\Component\Validator\Constraints\Image;

class BackgroundImage extends Image
{
	public $minWidth = 1200;
	public $maxSize = '1024k';
	public $allowSquare = false;
	public $allowLandscape = true;
	public $allowPortrait = false;
	public $detectCorrupted = true;

	public function validatedBy()
	{
		return 'background_image_validator';
	}
}
