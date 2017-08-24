<?php

namespace Busybee\Core\CalendarBundle\Entity;

use Busybee\Core\CalendarBundle\Model\GradeModel;
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
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $students;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $activities;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->students   = new ArrayCollection();
		$this->activities = new ArrayCollection();
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
	 * Add student
	 *
	 * @param \Busybee\People\StudentBundle\Entity\StudentGrade $student
	 *
	 * @return Grade
	 */
	public function addStudent(\Busybee\People\StudentBundle\Entity\StudentGrade $student)
	{
		if ($this->students->contains($student))
			return $this;

		$student->setGrade($this, false);

		$this->students->add($student);

		return $this;
	}

	/**
	 * Remove student
	 *
	 * @param \Busybee\People\StudentBundle\Entity\StudentGrade $student
	 */
	public function removeStudent(\Busybee\People\StudentBundle\Entity\StudentGrade $student)
	{
		$this->students->removeElement($student);
	}

	/**
	 * Get students
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getStudents()
	{
		return $this->students;
	}

	/**
	 * Add activity
	 *
	 * @param \Busybee\ActivityBundle\Entity\Activity $activity
	 *
	 * @return Grade
	 */
	public function addActivity(\Busybee\ActivityBundle\Entity\Activity $activity)
	{
		$this->activities[] = $activity;

		return $this;
	}

	/**
	 * Remove activity
	 *
	 * @param \Busybee\ActivityBundle\Entity\Activity $activity
	 */
	public function removeActivity(\Busybee\ActivityBundle\Entity\Activity $activity)
	{
		$this->activities->removeElement($activity);
	}

	/**
	 * Get activities
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getActivities()
	{
		return $this->activities;
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
}
