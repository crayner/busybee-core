<?php

namespace Busybee\DatabaseBundle\Validator\Constraints ;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager ;
use Symfony\Component\HttpFoundation\Request ;
use Busybee\DatabaseBundle\Entity\TableRepository ;

class NameTableValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {

		$entity = $this->request->request->get('database_field');
		$table = $entity['table'];
		$field_id = intval($this->request->attributes->get('field_id'));

		$qb = $this->entityManager->createQueryBuilder();
		$result = $qb->select('a')
			->from('Busybee\DatabaseBundle\Entity\Field', 'a')
			->innerjoin('a.table', 't')
			->where('a.name = :fieldName')
			->andwhere('t.id = :tableid')
			->setParameter('fieldName', $value)
			->setParameter('tableid', $table)
			->getQuery()
			->getResult()
		;

		if (is_array($result)) {
			foreach ($result as $w) 
			{
				if (intval($w->getId()) != intval($field_id) )
				{
					$this->context->buildViolation($constraint->message)
						->setParameter('%string%', $value)
						->addViolation();
				}
			}
		}
    }

	private $entityManager;
	private $request;
	
	public function __construct(EntityManager $entityManager, Request $request)
	{
		$this->entityManager = $entityManager;
		$this->request = $request;
	}
}