<?php
namespace Busybee\People\StudentBundle\Entity;

use Busybee\Core\CalendarBundle\Entity\CalendarGroup;
use Busybee\Core\SecurityBundle\Entity\User;
use Busybee\People\StudentBundle\Model\StudentCalendarGroupModel;

/**
 * Student Calendar Group
 */
class StudentCalendarGroup extends StudentCalendarGroupModel
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
	 * @var CalendarGroup
	 */
	private $calendarGroup;

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
	 * @return StudentCalendarGroup
	 */
	public function setStatus($status): StudentCalendarGroup
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
	 * @return StudentCalendarGroup
	 */
	public function setLastModified($lastModified): StudentCalendarGroup
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
	 * @return StudentCalendarGroup
	 */
	public function setCreatedOn($createdOn): StudentCalendarGroup
	{
		$this->createdOn = $createdOn;

		return $this;
	}

	/**
	 * Get calendarGroup
	 *
	 * @return CalendarGroup
	 */
	public function getCalendarGroup()
	{
		return $this->calendarGroup;
	}

	/**
	 * Set calendarGroup
	 *
	 * @param CalendarGroup $calendarGroup
	 *
	 * @return StudentCalendarGroup
	 */
	public function setCalendarGroup(CalendarGroup $calendarGroup = null, $add = true): StudentCalendarGroup
	{
		if ($add)
			$calendarGroup->addStudent($this, false);

		$this->calendarGroup = $calendarGroup;

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
	 * @return StudentCalendarGroup
	 */
	public function setCreatedBy(User $createdBy = null): StudentCalendarGroup
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
	 * @return StudentCalendarGroup
	 */
	public function setModifiedBy(User $modifiedBy = null): StudentCalendarGroup
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
	public function setStudent(Student $student): StudentCalendarGroup
	{
		$this->student = $student;

		return $this;
	}
}
