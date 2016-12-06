<?php
namespace Busybee\InstituteBundle\Model;

class Day extends \Busybee\InstituteBundle\Service\WidgetService\Day 
{

    private $closed = false;
	
    private $special = false;
	
    private $prompt;
	
	private $termBreak = false ;

    public function isClosed()
    {
        return $this->closed;
    }

    public function setClosed($value, $prompt)
    {
        $this->closed = (bool)$value;
		$this->prompt = $prompt;
    }

    public function isSpecial()
    {
        return $this->special;
    }

    public function setSpecial($value, $prompt)
    {
        $this->special = (bool)$value;
		$this->prompt = $prompt;
    }

    public function isTermBreak()
    {
        return $this->termBreak;
    }

    public function setTermBreak($termBreak)
    {
        $this->termBreak = (bool)$termBreak ;
    }

    public function isWeekEnd()
    {
        return in_array((int)$this->date->format('N'), array(0,6,7));
    }

    public function getPrompt()
    {
        return $this->prompt;
    }
}
