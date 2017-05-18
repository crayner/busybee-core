<?php

namespace Busybee\TimeTableBundle\Model;

use Busybee\InstituteBundle\Entity\SpecialDay;
use Busybee\InstituteBundle\Entity\Term;
use Busybee\InstituteBundle\Entity\Year;
use Busybee\SecurityBundle\Doctrine\UserManager;
use Busybee\SecurityBundle\Entity\User;
use Busybee\SystemBundle\Setting\SettingManager;
use Busybee\TimeTableBundle\Entity\Column;
use Busybee\TimeTableBundle\Entity\Day;
use Busybee\TimeTableBundle\Entity\Period;
use Busybee\TimeTableBundle\Entity\StartRotate;
use Busybee\TimeTableBundle\Entity\TimeTable;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class PeriodManager
{
    /**
     * @var ObjectManager
     */
    private $om;

    private $pr;

    /**
     * PeriodManager constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->pr = $om->getRepository(Period::class);
    }

    public function canDelete($id)
    {
        $period = $this->pr->find($id);
        if ($period->canDelete())
            return true;
        return false;
    }
}