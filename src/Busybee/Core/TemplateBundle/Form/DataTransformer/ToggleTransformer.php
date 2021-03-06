<?php

namespace Busybee\Core\TemplateBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ToggleTransformer implements DataTransformerInterface
{
	/**
	 * @param mixed $data
	 *
	 * @return boolean
	 */
	public function transform($data)
	{
		if (is_bool($data))
			return $data;

		return $data == 'Y' ? true : false;
	}

	/**
	 * @param mixed $data
	 *
	 * @return boolean
	 */
	public function reverseTransform($data)
	{
		if (is_bool($data))
			return $data;

		return $data === 'Y' ? true : false;
	}
}