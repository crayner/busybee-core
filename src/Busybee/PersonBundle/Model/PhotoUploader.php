<?php

namespace Busybee\PersonBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile ;
use Symfony\Component\HttpFoundation\File\File ;

class PhotoUploader
{
    private $targetDir;

    /**
     * PhotoUploader constructor.
     * @param $targetDir
     */
    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    /**
     * @param $data
     * @return null|string
     */
    public function upload($data)
    {
        if (is_null($data))
            return $data ;
        if ($data instanceof UploadedFile) {
            $fName = md5(uniqid()).'.'.$data->guessExtension();
            $path = str_replace('app/../', '', $this->targetDir);
            $data->move($path, $fName);

            $photo = new File($path.DIRECTORY_SEPARATOR.$fName, true);

            return $photo->getPathName();
        }
        return null ;
    }
}