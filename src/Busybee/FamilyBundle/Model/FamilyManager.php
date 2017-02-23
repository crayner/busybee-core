<?php
namespace Busybee\FamilyBundle\Model;

use Busybee\FamilyBundle\Entity\CareGiver;
use Busybee\PersonBundle\Entity\Person;
use Busybee\PersonBundle\Model\PersonManager;
use Busybee\SystemBundle\Setting\SettingManager;
use Doctrine\ORM\EntityManager;

class FamilyManager
{
    /**
     * @var SettingManager
     */
    private $sm;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var PersonManager
     */
    private $pm;

    /**
     * PersonManager constructor.
     * @param SettingManager $sm
     */
    public function __construct(SettingManager $sm, EntityManager $em, PersonManager $pm)
    {
        $this->sm = $sm;
        $this->em = $em;
        $this->pm = $pm;
    }

    /**
     * @param $data
     * @return null|string
     */
    public function generateFamilyName($data)
    {
        if (empty($data['careGiver']) || empty($data['careGiver'][0]['person']))
            return '';

        $cgr = $this->em->getRepository(Person::class);

        $cg1 = $cgr->find($data['careGiver'][0]['person']);
        $cg2 = null;
        if (!empty($data['careGiver'][1]))
            $cg2 = $cgr->find($data['careGiver'][1]['person']);

        $name = $cg1->getFormatName(['preferredOnly' => true]);

        if ($cg2 instanceof Person) {
            $name2 = $cg2->getFormatName(['preferredOnly' => true]);
            $surname = substr($name, 0, strpos($name, ':') + 1);
            $name2 = trim(str_replace($surname, '', $name2));
            if (!empty($name2))
                $name .= ' & ' . $name2;
        }

        return $name;
    }

    /**
     * @param $id
     * @return CareGiver|null|object
     */
    public function retrieveCareGiver($id)
    {
        if ($id > 0)
            return $this->em->getRepository(CareGiver::class)->find($id);
        return null;
    }

}