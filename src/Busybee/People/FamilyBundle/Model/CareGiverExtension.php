<?php

namespace Busybee\People\FamilyBundle\Model;


use Busybee\People\FamilyBundle\Entity\CareGiver;
use Symfony\Component\Translation\TranslatorInterface;

class CareGiverExtension extends \Twig_Extension
{
	/**
	 * @return string
	 */
	public function getName()
	{
		return 'caregiver_twig_extension';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('displayCareGiverField', array($this, 'displayCareGiverField')),
		);
	}

	/**
	 * @param CareGiver $cg
	 * @param string    $fieldName
	 * @param array     $options
	 *
	 * @return string
	 */
	public function displayCareGiverField($cg, $fieldName, $options = array())
	{
		if (!$cg instanceof CareGiver)
			return '';
		$name = 'get' . ucfirst($fieldName);
		if (!method_exists($cg, $name))
			return '';

		return $cg->$name($options);
	}
}