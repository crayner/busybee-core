<?php

namespace Busybee\Core\HomeBundle\Model;

class MathExtension extends \Twig_Extension
{
	/**
	 * @return string
	 */
	public function getName()
	{
		return 'math_twig_extension';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('intVal', array($this, 'intVal')),
		);
	}

	/**
	 * @param   string $value
	 *
	 * @return  int
	 */
	public function intVal($value)
	{
		return intval(trim($value));
	}
}