<?php

namespace Busybee\DatabaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Busybee\DatabaseBundle\Model\Field as FieldBase ;
use Busybee\DatabaseBundle\Validator\Constraints\NameTable ;

/**
 * Field
 */
class Field extends FieldBase
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
    private $prompt;

    /**
     * @var string
     */
    private $help;

    /**
     * @var string
     */
    private $type;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $table;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $role;

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
     * Set name
     *
     * @param string $name
     * @return Field
     */
    public function setName($name)
    {
		$this->name = str_replace(' ', '_', $name);

        return $this;
    }

    /**
     * Get prompt
     *
     * @return string 
     */
    public function getPrompt()
    {
        if (! empty($this->prompt))
			return $this->prompt;
		return $this->getDisplayName();
    }

    /**
     * Set prompt
     *
     * @param string Prompt
     * @return Field
     */
    public function setPrompt($prompt)
    {
		$this->prompt = $prompt;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return str_replace(' ', '_', $this->name);
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Field
     */
    public function setType($type)
    {
        $this->type = $type;

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
     * Constructor
     */
    public function __construct()
    {
        $this->table = new \Doctrine\Common\Collections\ArrayCollection();
        $this->role = new \Doctrine\Common\Collections\ArrayCollection();
		parent::__construct();
    }

    /**
     * Add table
     *
     * @param \Busybee\DatabaseBundle\Entity\Table $table
     * @return Field
     */
    public function addTable(\Busybee\DatabaseBundle\Entity\Table $table)
    {
        $this->table[] = $table;

        return $this;
    }

    /**
     * Remove table
     *
     * @param \Busybee\DatabaseBundle\Entity\Table $table
     */
    public function removeTable(\Busybee\DatabaseBundle\Entity\Table $table)
    {
        $this->table->removeElement($table);
    }

    /**
     * Set table
     *
 	 * @param \Busybee\DatabaseBundle\Entity\Table $table
     * @return Field 
     */
    public function setTable($table)
    {
        $this->table = new \Doctrine\Common\Collections\ArrayCollection();
		if (empty($table))
			return $this ;
		
		return $this->addTable($table);
    }

    /**
     * Get table
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTable()
    {
		return $this->table;
    }

    /**
     * Add role
     *
     * @param \Busybee\SecurityBundle\Entity\Role $role
     * @return Field
     */
    public function addRole(\Busybee\SecurityBundle\Entity\Role $role)
    {
        $this->role[] = $role;

        return $this;
    }

    /**
     * Remove role
     *
     * @param \Busybee\SecurityBundle\Entity\Role $role
     */
    public function removeRole(\Busybee\SecurityBundle\Entity\Role $role)
    {
        $this->role->removeElement($role);
    }

    /**
     * Get role
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set role
     *
	 * @param \Busybee\SecurityBundle\Entity\Role
     * @return Field 
     */
    public function setRole( $role )
    {
        $this->role = new \Doctrine\Common\Collections\ArrayCollection();
		if (empty($role))
			return $this ;
		
		return $this->addRole($role);
    }

    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return str_replace('_', ' ', $this->name);
    }
    /**
     * @var integer
     */
    private $sortkey;


    /**
     * Set sortkey
     *
     * @param integer $sortkey
     *
     * @return Field
     */
    public function setSortkey($sortkey)
    {
        $this->sortkey = $sortkey;

        return $this;
    }

    /**
     * Get sortkey
     *
     * @return integer
     */
    public function getSortkey()
    {
        return $this->sortkey;
    }
    /**
     * @var string
     */
    private $validator;


    /**
     * Set validator
     *
     * @param string $validator
     *
     * @return Field
     */
    public function setValidator($validator)
    {
        if (is_array($validator))
			$validator = $this->dumpYaml($validator);
        $this->validator = $validator;

        return $this;
    }

    /**
     * Get validator
     *
     * @return string
     */
    public function getValidator()
    {
		return $this->validator;
    }
	
	public function __toString()
	{
		return 'Field - '.$this->getField();
	}
    /**
     * @var string
     */
    private $parameters;


    /**
     * Set parameters
     *
     * @param string $parameters
     *
     * @return Field
     */
    public function setParameters($parameters)
    {
        if (is_array($parameters))
			$parameters = $this->dumpYaml($parameters);
		$this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return string
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get help
     *
     * @return string 
     */
    public function getHelp()
    {
		return $this->help;
    }

    /**
     * Set help
     *
     * @param string Help
     * @return Field
     */
    public function setHelp($help)
    {
		$this->help = $help;

        return $this;
    }


}
