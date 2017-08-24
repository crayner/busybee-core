<?php

namespace Busybee\FamilyBundle\Model;

class CareGiverModel
{
	use \Busybee\People\PersonBundle\Model\FormatNameExtension;

    public function __construct()
    {
        $this->setPhoneContact(false);
        $this->setSmsContact(false);
        $this->setMailContact(false);
        $this->setEmailContact(false);
        $this->setContactPriority(0);
        $this->setRelationship('Unknown');
    }

    public function __toString()
    {
        return strval($this->getId());
    }
}