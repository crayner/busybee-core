<?php

namespace Busybee\Core\TemplateBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

class ImageUploader
{
	private $targetDir;

	public function __construct($targetDir)
	{
		$this->targetDir = $targetDir;
	}

	public function upload(File $file)
	{
		if (!$file instanceof UploadedFile)
			return $file;
		$fName = md5(uniqid()) . '.' . $file->guessExtension();
		$path  = $this->targetDir;
		$file->move($path, $fName);

		$photo = new File($path . DIRECTORY_SEPARATOR . $fName, true);

		return $photo->getPathName();
	}
}