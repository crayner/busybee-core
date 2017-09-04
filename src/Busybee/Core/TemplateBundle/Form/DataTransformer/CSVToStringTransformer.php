<?php

namespace Busybee\Core\TemplateBundle\Form\DataTransformer;

use Busybee\Core\TemplateBundle\Model\FileUpLoad;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

class CSVToStringTransformer implements DataTransformerInterface
{
	private $loader;

	public function __construct(FileUpLoad $loader)
	{
		$this->loader = $loader;
	}

	/**
	 * Transforms an string to File
	 *
	 * @param mixed $data
	 *
	 * @return string
	 * @internal param Person|null $person
	 */
	public function transform($data)
	{
		$file = file_exists($data) ? $data : null;
		$file = is_null($file) ? new File(null, false) : new File($file, true);

		return $file;
	}

	/**
	 * Transforms a string (number) to an object (person).
	 *
	 * @param mixed $data
	 *
	 * @return Person|null
	 * @internal param string $personNumber
	 */
	public function reverseTransform($data)
	{
		if ($data instanceof File)
		{
			$data = $this->loader->upload($data);
		}

		return $data;
	}

	private function uploadFile($entity)
	{
		// upload only works for Person entities
		$fileName = $this->loader->upload($entity);

		return $fileName;
	}
}