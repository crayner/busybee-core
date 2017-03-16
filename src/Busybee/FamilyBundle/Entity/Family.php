<?php

namespace Busybee\FamilyBundle\Entity;

use Busybee\StudentBundle\Entity\Student;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Family
 */
class Family
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
     * @var \DateTime
     */
    private $lastModified;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var \Busybee\PersonBundle\Entity\Address
     */
    private $address1;

    /**
     * @var \Busybee\PersonBundle\Entity\Address
     */
    private $address2;

    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $modifiedBy;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $phone;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $students;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $careGiver;

    /**
     * @var string
     */
    private $firstLanguage;

    /**
     * @var string
     */
    private $secondLanguage;
    /**
     * @var string
     */
    private $house;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->phone = new ArrayCollection();
        $this->students = new ArrayCollection();
        $this->careGiver = new ArrayCollection();
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
     * @return Family
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @return Family
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
     * @return Family
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get address1
     *
     * @return \Busybee\PersonBundle\Entity\Address
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address1
     *
     * @param \Busybee\PersonBundle\Entity\Address $address1
     *
     * @return Family
     */
    public function setAddress1(\Busybee\PersonBundle\Entity\Address $address1 = null)
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * Get address2
     *
     * @return \Busybee\PersonBundle\Entity\Address
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set address2
     *
     * @param \Busybee\PersonBundle\Entity\Address $address2
     *
     * @return Family
     */
    public function setAddress2(\Busybee\PersonBundle\Entity\Address $address2 = null)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Busybee\SecurityBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdBy
     *
     * @param \Busybee\SecurityBundle\Entity\User $createdBy
     *
     * @return Family
     */
    public function setCreatedBy(\Busybee\SecurityBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get modifiedBy
     *
     * @return \Busybee\SecurityBundle\Entity\User
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Set modifiedBy
     *
     * @param \Busybee\SecurityBundle\Entity\User $modifiedBy
     *
     * @return Family
     */
    public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Add phone
     *
     * @param \Busybee\PersonBundle\Entity\Phone $phone
     *
     * @return Family
     */
    public function addPhone(\Busybee\PersonBundle\Entity\Phone $phone)
    {
        $this->phone[] = $phone;

        return $this;
    }

    /**
     * Remove phone
     *
     * @param \Busybee\PersonBundle\Entity\Phone $phone
     */
    public function removePhone(\Busybee\PersonBundle\Entity\Phone $phone)
    {
        $this->phone->removeElement($phone);
    }

    /**
     * Get phone
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Add emergencyContact
     *
     * @param CareGiver $emergencyContact
     *
     * @return Family
     */
    public function addEmergencyContact(CareGiver $emergencyContact)
    {
        $this->emergencyContact[] = $emergencyContact;

        return $this;
    }

    /**
     * Remove emergencyContact
     *
     * @param CareGiver $emergencyContact
     */
    public function removeEmergencyContact(CareGiver $emergencyContact)
    {
        $this->emergencyContact->removeElement($emergencyContact);
    }

    /**
     * Get emergencyContact
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmergencyContact()
    {
        return $this->emergencyContact;
    }

    /**
     * Add student
     *
     * @param  Student $student
     *
     * @return Family
     */
    public function addStudent(Student $student)
    {
        $this->students->add($student);

        return $this;
    }

    /**
     * Remove student
     *
     * @param Student $student
     */
    public function removeStudent(Student $student)
    {
        $this->students->removeElement($student);
    }

    /**
     * Get Students
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStudents()
    {
        return $this->students;
    }

    /**
     * Get Students
     *
     * @param \Doctrine\Common\Collections\Collection
     * @return Family
     */
    public function setStudents(ArrayCollection $students)
    {
        $this->students = $students;

        return $this;
    }

    /**
     * Add careGiver
     *
     * @param CareGiver $careGiver
     *
     * @return Family
     */
    public function addCareGiver(CareGiver $careGiver)
    {
        if (empty($careGiver->getFamily()))
            $careGiver->setFamily($this);

        $this->careGiver[] = $careGiver;

        return $this;
    }

    /**
     * Remove careGiver
     *
     * @param CareGiver $careGiver
     */
    public function removeCareGiver(CareGiver $careGiver)
    {
        $this->careGiver->removeElement($careGiver);
    }

    /**
     * Get careGiver
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCareGiver()
    {
        return $this->careGiver;
    }

    /**
     * Set careGiver
     *
     * @param   ArrayCollection
     * @return Family
     */
    public function setCareGiver(ArrayCollection $caregivers)
    {
        $this->careGiver = $caregivers;
        return $this;
    }

    /**
     * Get firstLanguage
     *
     * @return string
     */
    public function getFirstLanguage()
    {
        return $this->firstLanguage;
    }

    /**
     * Set firstLanguage
     *
     * @param string $firstLanguage
     *
     * @return Family
     */
    public function setFirstLanguage($firstLanguage)
    {
        $this->firstLanguage = $firstLanguage;

        return $this;
    }

    /**
     * Get secondLanguage
     *
     * @return string
     */
    public function getSecondLanguage()
    {
        return $this->secondLanguage;
    }

    /**
     * Set secondLanguage
     *
     * @param string $secondLanguage
     *
     * @return Family
     */
    public function setSecondLanguage($secondLanguage)
    {
        $this->secondLanguage = $secondLanguage;

        return $this;
    }

    /**
     * Get house
     *
     * @return string
     */
    public function getHouse()
    {
        return $this->house;
    }

    /**
     * Set house
     *
     * @param string $house
     *
     * @return Family
     */
    public function setHouse($house)
    {
        $this->house = $house;

        return $this;
    }
}
