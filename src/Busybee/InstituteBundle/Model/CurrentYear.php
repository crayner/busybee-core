<?php

namespace Busybee\InstituteBundle\Model;

use Busybee\SecurityBundle\Doctrine\UserManager;
use Busybee\InstituteBundle\Entity\Year;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CurrentYear
{
    /**
     * @var Year
     */
    private $currentYear;

    /**
     * CurrentYear constructor.
     * @param UserManager $um
     * @param TokenStorage $ts
     */
    public function __construct(UserManager $um, TokenStorage $ts)
    {
        $this->currentYear = $um->getSystemYear($ts->getToken()->getUser());
    }

    /**
     * @return Year
     */
    public function getCurrentYear()
    {
        return $this->currentYear;
    }
}