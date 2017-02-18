<?php
namespace Busybee\FormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File ;
use Busybee\FormBundle\Model\ImageUploader ;

class ImageToStringTransformer implements DataTransformerInterface
{
    /**
     * @var ImageUploader
     */
    private $uploader;

    /**
     * ImageToStringTransformer constructor.
     * @param ImageUploader $uploader
     */
    public function __construct(ImageUploader $uploader)
	{
        $this->uploader = $uploader;
	}
    /**
     * Transforms an string to File
     *
     * @param  Person|null $person
     * @return string
     */
    public function transform($data)
    {
        $file = file_exists($data) ? $data : null;
		$file = is_null($file) ? new File(null, false) : new File($file, true) ;
		return $file ;
    }

    /**
     * Transforms a string (number) to an object (person).
     *
     * @param  string $personNumber
     * @return Person|null
     * @throws TransformationFailedException if object (Person) is not found.
     */
    public function reverseTransform($data)
    {
        if ($data instanceof File) {
            $data = $this->uploader->upload($data);
        }
        return $data;
    }

    private function uploadFile($entity)
    {
        // upload only works for Person entities
        if ($entity instanceof Person)
        {
			$fileName = $this->uploader->upload($entity);
			$entity->setPhoto($fileName);
		}
    }
}