<?php
namespace Busybee\InstituteBundle\Validator\Constraints ;

use Busybee\InstituteBundle\Entity\Term;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use DateTime ;

class TermDateValidator extends ConstraintValidator
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * SpecialDayDateValidator constructor.
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->om = $objectManager;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     * @return mixed|void
     */
    public function validate($value, Constraint $constraint)
    {
        
        if (empty($value))
            return ;

        $firstDay = $constraint->year->getFirstDay();
        $lastDay = $constraint->year->getLastDay();

        $terms = $this->om->getRepository(Term::class)->findBy(['year' => $constraint->year->getId()], ['firstDay' => 'ASC']);

        if (!empty($terms))
            foreach ($terms as $t) {
                if (!$value->contains($t))
                    if (!$t->canDelete()) {
                        $this->context->buildViolation('year.term.error.delete', ['%term%' => $t->getName()])
                            ->addViolation();
                        return;
                    }
            }

        foreach ($value as $key => $term) {
            if (empty($term->getName()) || empty($term->getnameShort())) {
                $value->remove($key);
                $constraint->year->removeTerm($term);
            }
        }

        $terms = array();
		
		foreach($value as $term)
		{
			if (! $term->getFirstDay() instanceof DateTime  || ! $term->getLastDay() instanceof DateTime)
			{
                $this->context->buildViolation('year.term.error.invalid')
					->addViolation();
				return ;
			}
            if ($term->getFirstDay() > $term->getLastDay()) {
                $this->context->buildViolation('year.term.error.order')
                    ->addViolation();
                return;
            }
            if ($term->getFirstDay() < $firstDay)
			{
                $this->context->buildViolation('year.term.error.outsideYear', array('%term_date%' => $term->getFirstDay()->format('jS M Y'), '%year_date%' => $yearStart->format('jS M Y'), '%operator%' => '<'))
					->addViolation();
				return ;
			}
            if ($term->getLastDay() > $lastDay)
			{
                $this->context->buildViolation('year.term.error.outsideYear', array('%term_date%' => $term->getLastDay()->format('jS M Y'), '%year_date%' => $yearEnd->format('jS M Y'), '%operator%' => '>'))
					->addViolation();
				return ;
			}
			foreach($terms as $name=>$test)
			{
				if ($term->getFirstDay() >= $test['start'] && $term->getFirstDay() <= $test['end']) 
				{
                    $this->context->buildViolation('year.term.error.overlapped', array('%name1%' => $name, '%name2%' => $term->getName()))
						->addViolation();
					return ;
				}
				if ($term->getLastDay() >= $test['start'] && $term->getLastDay() <= $test['end']) 
				{
                    $this->context->buildViolation('year.term.error.overlapped', array('%name1%' => $name, '%name2%' => $term->getName()))
						->addViolation();
					return ;
				}
			}
			$terms[$term->getName()]['start'] = $term->getFirstDay();
			$terms[$term->getName()]['end'] = $term->getLastDay();
		}
		
		return $value ;
    }
}