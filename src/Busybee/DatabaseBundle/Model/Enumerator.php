<?php

namespace Busybee\DatabaseBundle\Model;

use Symfony\Component\Yaml\Parser ;
use Symfony\Component\Yaml\Dumper ;
/**
 * Field Base
 */
class Enumerator
{
	
	/**
	 * get Yaml
	 * @param	string 
	 * @return 	string yaml formated
	 */
	 public function parseYaml($value)
	 {
		 $this->yaml = new Parser();
		 return $this->yaml->parse($value);
	 }

	/**
	 * set Yaml
	 * @param	string yaml formated
	 * @return 	string yaml formated
	 */
	 public function dumpYaml($value)
	 {
		 $this->yaml = new Dumper();
		 return $this->yaml->dump($value);
	 }

	public function getPromptString()
	{
		if (is_array($this->getPrompt()))
			return 'SubArray:'.$this->dumpYaml($this->getPrompt());
		return $this->getPrompt();
	}
}
