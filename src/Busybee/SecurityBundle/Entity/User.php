<?php

namespace Busybee\SecurityBundle\Entity;

use Busybee\SecurityBundle\Model\User as UserModel;
use DateTime ;
/**
 * User
 */
class User extends UserModel
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $usernameCanonical;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $emailCanonical;

    /**
     * @var boolean
     */
    protected $enabled;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var \DateTime
     */
    protected $lastLogin;

    /**
     * @var boolean
     */
    protected $locked;

    /**
     * @var boolean
     */
    protected $expired;

    /**
     * @var \DateTime
     */
    protected $expiresAt;

    /**
     * @var string
     */
    protected $confirmationToken;

    /**
     * @var \DateTime
     */
    protected $passwordRequestedAt;

    /**
     * @var boolean
     */
    protected $credentialsExpired;

    /**
     * @var \DateTime
     */
    protected $credentialsExpireAt;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $groups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $directroles;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $lastModified;
    /**
     * @var \DateTime
     */
    private $createdOn;
    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $createdBy;
    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $modifiedBy;
    /**
     * @var \Busybee\PersonBundle\Entity\Person
     */
    private $person;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->directroles = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return User
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set locale
     *
     * @param string $locale
     *
     * @return User
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     *
     * @return User
     */
    public function setLastLogin(DateTime $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     *
     * @return User
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get expired
     *
     * @return boolean
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Set expired
     *
     * @param boolean $expired
     *
     * @return User
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;

        return $this;
    }

    /**
     * Get expiresAt
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Set expiresAt
     *
     * @param \DateTime $expiresAt
     *
     * @return User
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Get confirmationToken
     *
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * Set confirmationToken
     *
     * @param string $confirmationToken
     *
     * @return User
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * Get passwordRequestedAt
     *
     * @return \DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * Set passwordRequestedAt
     *
     * @param \DateTime $passwordRequestedAt
     *
     * @return User
     */
    public function setPasswordRequestedAt(DateTime $passwordRequestedAt = null)
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

        return $this;
    }

    /**
     * Get credentialsExpired
     *
     * @return boolean
     */
    public function getCredentialsExpired()
    {
        return $this->credentialsExpired;
    }

    /**
     * Set credentialsExpired
     *
     * @param boolean $credentialsExpired
     *
     * @return User
     */
    public function setCredentialsExpired($credentialsExpired)
    {
        $this->credentialsExpired = $credentialsExpired;

        return $this;
    }

    /**
     * Get credentialsExpireAt
     *
     * @return \DateTime
     */
    public function getCredentialsExpireAt()
    {
        return $this->credentialsExpireAt;
    }

    /**
     * Set credentialsExpireAt
     *
     * @param \DateTime $credentialsExpireAt
     *
     * @return User
     */
    public function setCredentialsExpireAt($credentialsExpireAt)
    {
        $this->credentialsExpireAt = $credentialsExpireAt;

        return $this;
    }

    /**
     * Add group
     *
     * @param \Busybee\SecurityBundle\Entity\Group $group
     *
     * @return User
     */
    public function addGroup(\Busybee\SecurityBundle\Entity\Group $group)
    {
        $this->groups[] = $group;

        return $this;
    }

    /**
     * Remove group
     *
     * @param \Busybee\SecurityBundle\Entity\Group $group
     */
    public function removeGroup(\Busybee\SecurityBundle\Entity\Group $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add directrole
     *
     * @param Role $directrole
     *
     * @return User
     */
    public function addDirectrole(Role $directrole)
    {
        $this->directroles[] = $directrole;

        return $this;
    }

    /**
     * Remove directrole
     *
     * @param Role $directrole
     */
    public function removeDirectrole(Role $directrole)
    {
        $this->directroles->removeElement($directrole);
    }

    /**
     * Get directroles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDirectroles()
    {
        return $this->directroles;
    }

    /**
     * check Username
     *
     * @return void
     */
    public function checkUsername()
    {
        if (empty($this->getUsername()))
			$this->setUsername($this->getEmail());
        if (empty($this->getUsernameCanonical()))
			$this->setUsernameCanonical($this->getEmailCanonical());
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get usernameCanonical
     *
     * @return string
     */
    public function getUsernameCanonical()
    {
        return $this->usernameCanonical;
    }

    /**
     * Set usernameCanonical
     *
     * @param string $usernameCanonical
     *
     * @return User
     */
    public function setUsernameCanonical($usernameCanonical)
    {
        $this->usernameCanonical = $usernameCanonical;

        return $this;
    }

    /**
     * Get emailCanonical
     *
     * @return string
     */
    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    /**
     * Set emailCanonical
     *
     * @param string $emailCanonical
     *
     * @return User
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;

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
     * @return Setting
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
     * @return User
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

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
     * @return User
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
     * @return User
     */
    public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Get person
     *
     * @return \Busybee\PersonBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set person
     *
     * @param \Busybee\PersonBundle\Entity\Person $person
     *
     * @return User
     */
    public function setPerson(\Busybee\PersonBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }
}
