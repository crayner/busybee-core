<?php
namespace Busybee\InstituteBundle\Model;

class Month extends \Busybee\InstituteBundle\Service\WidgetService\Month
{

    public function getIsSummer()
    {
        return in_array($this->getNumber(), array(11,12,1));
    }
}