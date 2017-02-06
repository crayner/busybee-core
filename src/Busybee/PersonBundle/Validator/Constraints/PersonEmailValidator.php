<?php
namespace Busybee\PersonBundle\Validator\Constraints ;

use Busybee\PersonBundle\Entity\Person;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint ;
use Symfony\Component\Validator\ConstraintValidator;


class PersonEmailValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    private $em ;

    /**
     * PersonEmailValidator constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em ;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (empty($value))
            return ;

        $object = $this->context->getObject();

        if ($constraint->errorPath == 'email') {

            $result = $this->em->getRepository(Person::class)->createQueryBuilder('p')
                ->select('p.id')
                ->where('p.email = :email')
                ->orWhere('p.email2 = :email')
                ->andWhere('p.id <> :id')
                ->setParameter('email', $value)
                ->setParameter('id', $object->getId())
                ->getQuery()
                ->getResult();
           if (! empty($result)) {
               $this->context->buildViolation($constraint->message)
                   ->setParameter('%string%', $value)
                   ->addViolation();
           }
        }
        if ($constraint->errorPath == 'email2') {


            $result = $this->em->getRepository(Person::class)->createQueryBuilder('p')
                ->select('p.id')
                ->where('p.email = :email')
                ->orWhere('p.email2 = :email')
                ->andWhere('p.id <> :id')
                ->setParameter('email', $value)
                ->setParameter('id', $object->getId())
                ->getQuery()
                ->getResult();
            if (! empty($result)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%string%', $value)
                    ->addViolation();
            }
        }
    }
}