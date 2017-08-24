<?php
namespace Busybee\InstituteBundle\Entity;

use Busybee\InstituteBundle\Model\DepartmentModel;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Department
 */
class Department extends DepartmentModel
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $nameShort;

    /**
     * @var \DateTime
     */
    private $lastModified;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var \Busybee\Core\SecurityBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Busybee\Core\SecurityBundle\Entity\User
     */
    private $modifiedBy;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $staff;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $courses;

    /**
     * Department constructor.
     */
    public function __construct()
    {
        $this->staff = new ArrayCollection();
        $this->courses = new ArrayCollection();
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Department
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Department
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get nameShort
     *
     * @return string
     */
    public function getNameShort()
    {
        return $this->nameShort;
    }

    /**
     * Set nameShort
     *
     * @param string $nameShort
     *
     * @return Department
     */
    public function setNameShort($nameShort)
    {
        $this->nameShort = $nameShort;

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
     * @return Department
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
     * @return Department
     */
	public function setModifiedBy(\Busybee\Core\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Add staff
     *
     * @param \Busybee\InstituteBundle\Entity\DepartmentStaff $staff
     *
     * @return Department
     */
    public function addStaff(\Busybee\InstituteBundle\Entity\DepartmentStaff $staff)
    {
        if ($this->staff->contains($staff))
            return $this;

        $staff->setDepartment($this);

        $this->staff->add($staff);

        return $this;
    }

    /**
     * Remove staff
     *
     * @param \Busybee\InstituteBundle\Entity\DepartmentStaff $staff
     */
    public function removeStaff(\Busybee\InstituteBundle\Entity\DepartmentStaff $staff)
    {
        $this->staff->removeElement($staff);
    }

    /**
     * Get staff
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStaff($sorted = true)
    {
        if ($sorted)
            return $this->sortStaff();
        return $this->staff;
    }

    /**
     * Set staff
     *
     * @return Department
     */
    public function setStaff(ArrayCollection $staff)
    {
        $this->staff = $staff;

        return $this;
    }

    /**
     * Add course
     *
     * @param \Busybee\CurriculumBundle\Entity\Course $course
     *
     * @return Department
     */
    public function addCourse(\Busybee\CurriculumBundle\Entity\Course $course)
    {
        if ($this->courses->contains($course))
            return $this;

        $this->courses->add($course);

        return $this;
    }

    /**
     * Remove course
     *
     * @param \Busybee\CurriculumBundle\Entity\Course $course
     */
    public function removeCourse(\Busybee\CurriculumBundle\Entity\Course $course)
    {
        $this->courses->removeElement($course);
    }

    /**
     * Get courses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourses($sorted = true)
    {
        if ($sorted)
            return $this->sortCourses();
        return $this->courses;
    }

    /**
     * Set courses
     *
     * @return Department
     */
    public function setCourses(ArrayCollection $courses)
    {
        $this->courses = $courses;

        return $this;
    }
}
