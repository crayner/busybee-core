<?php

namespace Busybee\PersonBundle\Form\Transformer ;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Bundle\DoctrineBundle\Registry as ObjectManager ;
use Busybee\PersonBundle\Entity\Image ;
use Symfony\Component\HttpFoundation\File\File ;

class PhotoTransformer implements DataTransformerInterface
{
    private $manager;
    private $path;

    public function __construct(ObjectManager $manager, $path)
    {
        $this->manager = $manager;
		$this->uploadPath = $path;
    }
	
	public function transform($val)
    {
        if (null === $val) {
            return '';
        }
		$photo = $this->manager->getRepository('BusybeePersonBundle:Image')->findOneBy(array('id' => $val->getId()));
		$file = new File($photo->getPath(), true);
		$file->id = $val->getId();

        return $file;
    }

    public function reverseTransform($val)
    {
		if ($val instanceof Image)
			return $val ;

        if (! $val) 
            return null;
		
		if ($val->getError() !== 0)
			return null;


        $photo = new Image();
		$photo->setMimeType($val->getClientMimeType());
		$photo->setName($val->getClientOriginalName());
		$photo->setSize($val->getClientSize());
		
		$fName = md5(uniqid()).'.'.$val->guessExtension();
		$path = str_replace('app/../', '', $this->uploadPath);
        $val->move($path, $fName);
		$photo->setPath($path.'/'.$fName);

        return $photo;
    }

}