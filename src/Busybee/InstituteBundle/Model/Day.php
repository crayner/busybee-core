<?php
namespace Busybee\InstituteBundle\Model;

class Day extends \Busybee\InstituteBundle\Service\WidgetService\Day 
{
    /**
     * @var bool
     */
    private $closed = false;

    /**
     * @var bool
     */
    private $special = false;

    /**
     * @var bool
     */
	private $termBreak = false ;

    /**
     * @var null
     */
    private $prompt = null ;

    /**
     * @var bool
     */
    private $schoolDay = false;

    /**
     * @return bool
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * @param $value
     * @param $prompt
     */
    public function setClosed($value, $prompt)
    {
        $this->closed = (bool)$value;
		$this->prompt = $prompt;
    }

    /**
     * @return bool
     */
    public function isSpecial()
    {
        return $this->special;
    }

    /**
     * @param $value
     * @param $prompt
     */
    public function setSpecial($value, $prompt)
    {
        $this->special = (bool)$value;
		$this->prompt = $prompt;
    }

    /**
     * @return bool
     */
    public function isTermBreak()
    {
        return $this->termBreak;
    }

    public function setTermBreak($termBreak)
    {
        $this->termBreak = (bool)$termBreak ;
    }

    /**
     * @return bool
     */
    public function isWeekEnd()
    {
        return in_array((int)$this->date->format('N'), array(0,6,7));
    }

    /**
     * @return null
     */
    public function getPrompt()
    {
        return $this->prompt;
    }

    /**
     * @return bool
     */
    public function getSchoolDay()
    {
        return $this->schoolDay;
    }

    /**
     * @param bool $schoolDay
     * @return Day
     */
    public function setSchoolDay(bool $schoolDay): Day
    {
        $this->schoolDay = $schoolDay;

        return $this;
    }
}
