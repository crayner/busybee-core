<?php

namespace Busybee\Core\TemplateBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

class FileUpLoad
{
	/**
	 * @var string
	 */
	private $targetDir;

	/**
	 * FileUpLoad constructor.
	 *
	 * @param $targetDir
	 */
	public function __construct($targetDir)
	{
		$this->targetDir = $targetDir;
	}

	/**
	 * @param File $file
	 *
	 * @return string|File
	 */
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