<?php

namespace Busybee\DatabaseBundle\Entity;

use Busybee\DatabaseBundle\Model\Enumerator as EnumeratorBase ;
/**
 * Enumerator
 */
class Enumerator extends EnumeratorBase
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
    private $value;

    /**
     * @var string
     */
    private $prompt;


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
     *
     * @return Enumerator
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set value
     *
     * @param string $value
     *
     * @return Enumerator
     */
    public function setValue($value)
    {
        $this->value = strval($value);

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set prompt
     *
     * @param string $prompt
     *
     * @return Enumerator
     */
    public function setPrompt($prompt)
    {
		if (is_array($prompt))
			$prompt = 'SubArray:'.$this->dumpYaml($prompt);
		$this->prompt = $prompt;

        return $this;
    }

    /**
     * Get prompt
     *
     * @return string / array
     */
    public function getPrompt()
    {
     	if (0 === strpos($this->prompt, 'SubArray:'))
			return $this->parseYaml(str_replace('SubArray:', '', $this->prompt));
	    return $this->prompt;
    }
}
