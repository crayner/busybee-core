<?php

namespace Busybee\PersonBundle\Model;

use Busybee\FamilyBundle\Entity\CareGiver;
use Busybee\PersonBundle\Entity\Person;
use Busybee\SecurityBundle\Entity\User;
use Busybee\StaffBundle\Entity\Staff;
use Busybee\StudentBundle\Entity\Student;
use Doctrine\Common\CommonException;

trait FormatNameExtension
{
    /**
     * @return string
     * @throws CommonException
     */
    public function getFullName($options = array())
    {
        return $this->getFormatName($options);
    }

    /**
     * @return string
     * @throws CommonException
     */
    public function getFormatName($options = array())
    {
        if ($this instanceof CareGiver) {
            if (empty($options)) {
                $options['preferredOnly'] = true;
            }
            $person = $this->getPerson();
            if ($person instanceof Person)
                return $person->getFormatName($options);
        }

        if ($this instanceof User) {
            $person = $this->getPerson();
            if ($person instanceof Person)
                return $person->getFormatName($options);
            else
                return $this->getUsername();
        }

        if ($this instanceof Staff) {
            $person = $this->getPerson();
            if ($person instanceof Person)
                return $person->getFormatName($options);
        }

        if ($this instanceof Student) {
            $person = $this->getPerson();
            if (empty($options)) {
                $options['preferredOnly'] = true;
                $options['surnameFirst'] = false;
            }
            if ($person instanceof Person)
                return $person->getFormatName($options);
        }

        throw new CommonException('The record ' . __CLASS__ . ' does not have a valid person.');
    }
}
