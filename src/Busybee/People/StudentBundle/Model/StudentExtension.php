<?php

namespace Busybee\People\StudentBundle\Model;


use Busybee\People\StudentBundle\Entity\Student;
use Busybee\People\StudentBundle\Repository\StudentRepository;
use Symfony\Component\Translation\TranslatorInterface;

class StudentExtension extends \Twig_Extension
{
	/**
	 * @var StudentRepository
	 */
	private $sr;

	/**
	 * @var TranslatorInterface
	 */
	private $translator;

	/**
	 * PersonExtension constructor.
	 *
	 * @param array $buttons
	 */
	public function __construct(StudentRepository $sr, TranslatorInterface $translator)
	{
		$this->sr         = $sr;
		$this->translator = $translator;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'student_twig_extension';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('displayStudentField', array($this, 'displayStudentField')),
		);
	}

	/**
	 * @param Student $student
	 * @param string  $fieldName
	 * @param array   $options
	 *
	 * @return string
	 */
	public function displayStudentField($student, $fieldName, $options = array())
	{
		if (!$student instanceof Student)
			return '';
		$name = 'get' . ucfirst($fieldName);
		if (!method_exists($student, $name))
			return '';

		return $student->$name($options);
	}
}