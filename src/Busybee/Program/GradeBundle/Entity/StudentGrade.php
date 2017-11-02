<?php

namespace Busybee\Program\GradeBundle\Entity;

use Busybee\Core\CalendarBundle\Entity\Grade;
use Busybee\Core\SecurityBundle\Entity\User;
use Busybee\Program\GradeBundle\Model\StudentGradeModel;
use Busybee\People\StudentBundle\Entity\Student;

/**
 * StudentGrade
 */
class StudentGrade extends StudentGradeModel
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $status;

	/**
	 * @var \DateTime
	 */
	private $lastModified;

	/**
	 * @var \DateTime
	 */
	private $createdOn;

	/**
	 * @var Student
	 */
	private $student;

	/**
	 * @var Grade
	 */
	private $grade;

	/**
	 * @var User
	 */
	private $createdBy;

	/**
	 * @var User
	 */
	private $modifiedBy;


	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get status
	 *
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Set status
	 *
	 * @param string $status
	 *
	 * @return StudentGrade
	 */
	public function setStatus($status): StudentGrade
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * Get lastModified
	 *
	 * @return \DateTime
	 */
	public function getLastModified()
	{
		return $this->lastModified;
	}

	/**
	 * Set lastModified
	 *
	 * @param \DateTime $lastModified
	 *
	 * @return StudentGrade
	 */
	public function setLastModified($lastModified): StudentGrade
	{
		$this->lastModified = $lastModified;

		return $this;
	}

	/**
	 * Get createdOn
	 *
	 * @return \DateTime
	 */
	public function getCreatedOn()
	{
		return $this->createdOn;
	}

	/**
	 * Set createdOn
	 *
	 * @param \DateTime $createdOn
	 *
	 * @return StudentGrade
	 */
	public function setCreatedOn($createdOn): StudentGrade
	{
		$this->createdOn = $createdOn;

		return $this;
	}

	/**
	 * Get grade
	 *
	 * @return Grade
	 */
	public function getGrade()
	{
		return $this->grade;
	}

	/**
	 * Set grade
	 *
	 * @param Grade $grade
	 *
	 * @return StudentGrade
	 */
	public function setGrade(Grade $grade = null, $add = true): StudentGrade
	{
		$this->grade = $grade;

		return $this;
	}

	/**
	 * Get createdBy
	 *
	 * @return User
	 */
	public function getCreatedBy()
	{
		return $this->createdBy;
	}

	/**
	 * Set createdBy
	 *
	 * @param User $createdBy
	 *
	 * @return StudentGrade
	 */
	public function setCreatedBy(User $createdBy = null): StudentGrade
	{
		$this->createdBy = $createdBy;

		return $this;
	}

	/**
	 * Get modifiedBy
	 *
	 * @return User
	 */
	public function getModifiedBy()
	{
		return $this->modifiedBy;
	}

	/**
	 * Set modifiedBy
	 *
	 * @param User $modifiedBy
	 *
	 * @return StudentGrade
	 */
	public function setModifiedBy(User $modifiedBy = null): StudentGrade
	{
		$this->modifiedBy = $modifiedBy;

		return $this;
	}

	/**
	 * Get Student
	 *
	 * @return Student|null
	 */
	public function getStudent()
	{
		return $this->student;
	}

	/**
	 * Set Student
	 *
	 * @param Student $student
	 */
	public function setStudent(Student $student): StudentGrade
	{
		$this->student = $student;

		return $this;
	}
}
