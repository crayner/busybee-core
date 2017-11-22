<?php

namespace Busybee\Core\CalendarBundle\Entity;

use Busybee\Core\CalendarBundle\Model\GradeModel;
use Busybee\Facility\InstituteBundle\Entity\Space;
use Busybee\People\StaffBundle\Entity\Staff;
use Busybee\Program\GradeBundle\Entity\StudentGrade;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Grade
 */
class Grade extends GradeModel
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $grade;

	/**
	 * @var \Busybee\Core\CalendarBundle\Entity\Year
	 */
	private $year;

	/**
	 * @var \Busybee\Core\SecurityBundle\Entity\User
	 */
	private $createdBy;

	/**
	 * @var \Busybee\Core\SecurityBundle\Entity\User
	 */
	private $modifiedBy;

	/**
	 * @var integer
	 */
	private $sequence;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var \DateTime
	 */
	private $lastModified;

	/**
	 * @var \DateTime
	 */
	private $createdOn;

	/**
	 * @var ArrayCollection
	 */
	private $students;

	/**
	 * @var Staff
	 */
	private $tutor1;

	/**
	 * @var Staff
	 */
	private $tutor2;

	/**
	 * @var Staff
	 */
	private $tutor3;

	/**
	 * @var Space
	 */
	private $space;

	/**
	 * @var string
	 */
	private $website;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->students   = new ArrayCollection();
	}

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
	 * Get grade
	 *
	 * @return string
	 */
	public function getGrade()
	{
		return $this->grade;
	}

	/**
	 * Set grade
	 *
	 * @param string $grade
	 *
	 * @return Grade
	 */
	public function setGrade($grade)
	{
		$this->grade = $grade;

		return $this;
	}

	/**
	 * Get year
	 *
	 * @return \Busybee\Core\CalendarBundle\Entity\Year
	 */
	public function getYear()
	{
		return $this->year;
	}

	/**
	 * Set year
	 *
	 * @param \Busybee\Core\CalendarBundle\Entity\Year $year
	 *
	 * @return Grade
	 */
	public function setYear(\Busybee\Core\CalendarBundle\Entity\Year $year = null)
	{
		$this->year = $year;

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
	 * @return Department
	 */
	public function setLastModified($lastModified)
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
	 * @return Department
	 */
	public function setCreatedOn($createdOn)
	{
		$this->createdOn = $createdOn;

		return $this;
	}

	/**
	 * Get createdBy
	 *
	 * @return \Busybee\Core\SecurityBundle\Entity\User
	 */
	public function getCreatedBy()
	{
		return $this->createdBy;
	}

	/**
	 * Set createdBy
	 *
	 * @param \Busybee\Core\SecurityBundle\Entity\User $createdBy
	 *
	 * @return Grade
	 */
	public function setCreatedBy(\Busybee\Core\SecurityBundle\Entity\User $createdBy = null)
	{
		$this->createdBy = $createdBy;

		return $this;
	}

	/**
	 * Get modifiedBy
	 *
	 * @return \Busybee\Core\SecurityBundle\Entity\User
	 */
	public function getModifiedBy()
	{
		return $this->modifiedBy;
	}

	/**
	 * Set modifiedBy
	 *
	 * @param \Busybee\Core\SecurityBundle\Entity\User $modifiedBy
	 *
	 * @return Grade
	 */
	public function setModifiedBy(\Busybee\Core\SecurityBundle\Entity\User $modifiedBy = null)
	{
		$this->modifiedBy = $modifiedBy;

		return $this;
	}

	/**
	 * Get sequence
	 *
	 * @return integer
	 */
	public function getSequence()
	{
		return $this->sequence;
	}

	/**
	 * Set sequence
	 *
	 * @param integer $sequence
	 *
	 * @return Grade
	 */
	public function setSequence($sequence)
	{
		$this->sequence = $sequence;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		if (empty($this->name))
			return $this->grade;

		return $this->name;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Grade
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get Students
	 *
	 * @return ArrayCollection
	 */
	public function getStudents(): ArrayCollection
	{
		return $this->students;
	}

	/**
	 * Set Students
	 *
	 * @param ArrayCollection $students
	 *
	 * @return Grade
	 */
	public function setStudents(ArrayCollection $students): Grade
	{
		$this->students = $students;

		return $this;
	}

	/**
	 * Add Student
	 *
	 * @param StudentGrade|null $student
	 *
	 * @return Grade
	 */
	public function addStudent(StudentGrade $student = null): Grade
	{
		if (!$student instanceof StudentGrade)
			return $this;

		if (!$this->students->contains($student))
			$this->students->add($student);

		return $this;
	}

	/**
	 * Remove Student
	 *
	 * @param StudentGrade $student
	 *
	 * @return Grade
	 */
	public function removeStudent(StudentGrade $student): Grade
	{
		$this->students->removeElement($student);

		return $this;
	}

	/**
	 * @return Staff|null
	 */
	public function getTutor1(): ?Staff
	{
		return $this->tutor1;
	}

	/**
	 * @param Staff $tutor1
	 *
	 * @return StudentGrade
	 */
	public function setTutor1(Staff $tutor1 = null): Grade
	{
		$this->tutor1 = $tutor1;

		return $this;
	}

	/**
	 * @return Staff|null
	 */
	public function getTutor2(): ?Staff
	{
		return $this->tutor2;
	}

	/**
	 * @param Staff $tutor2
	 *
	 * @return StudentGrade
	 */
	public function setTutor2(Staff $tutor2 = null): Grade
	{
		$this->tutor2 = $tutor2;

		return $this;
	}

	/**
	 * @return Staff|null
	 */
	public function getTutor3(): ?Staff
	{
		return $this->tutor3;
	}

	/**
	 * @param Staff|null $tutor3
	 *
	 * @return Grade
	 */
	public function setTutor3(Staff $tutor3 = null): Grade
	{
		$this->tutor3 = $tutor3;

		return $this;
	}

	/**
	 * @return Space|null
	 */
	public function getSpace(): ?Space
	{
		return $this->space;
	}

	/**
	 * @param Space $space
	 *
	 * @return Grade
	 */
	public function setSpace(Space $space = null): Grade
	{
		$this->space = $space;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getWebsite(): ?string
	{
		return $this->website;
	}

	/**
	 * @param string $website
	 *
	 * @return Grade
	 */
	public function setWebsite(string $website): Grade
	{
		$this->website = $website;

		return $this;
	}

}
