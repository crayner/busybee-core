<?php

namespace General\ValidationBundle\Model;

use Doctrine\ORM\Mapping as ORM ;
use Doctrine\Common\Collections\ArrayCollection ;

/**
 * Validator
 */
class Validator
{
    public function __construct()
    {
    
        $this->setUnique(false);
        $this->setNotblank(false);
        $this->setConstraintgroup('Default');
        $this->getMessage();
    }
    
    protected function testMessages($messages)
    {
        $now = $messages;
        $messages = array(
            'default'=>'',
            'notblank'=>'',
            'unique'=>'',
            'match'=>'',
            'minlength'=>'',
            'length'=>'',
            'enumeration'=>'',
        );
        foreach ($messages as $q=>$w){
            if (isset($now[$q]))
                $messages[$q] = $now[$q];
        }
        $result = array();
dump($messages);
        foreach ($messages as $q=>$w){
            if (isset($now[$q]))
                $result[] = array('message_key'=>$q, 'message_value'=>$w);
        }
        $this->setMessage($result);
dump($result);
        return $result;
    }
}