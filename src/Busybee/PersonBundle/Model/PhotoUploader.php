<?php

namespace Busybee\PersonBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile ;
use Symfony\Component\HttpFoundation\File\File ;
use Busybee\PersonBundle\Entity\Person ;

class PhotoUploader
{
    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(Person $person)
    {
		$file = $person->getPhoto();
		if (! $file instanceof UploadedFile)
			return $file ;
		$fName = md5(uniqid()).'.'.$file->guessExtension();
		$path = str_replace('app/../', '', $this->targetDir);
        $file->move($path, $fName);

		$photo = new File($path.DIRECTORY_SEPARATOR.$fName, true);

        return $photo->getPathName();
    }
}