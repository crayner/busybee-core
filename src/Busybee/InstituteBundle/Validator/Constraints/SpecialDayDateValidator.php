<?php
namespace Busybee\InstituteBundle\Validator\Constraints ;

use Busybee\InstituteBundle\Entity\SpecialDay;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraint ;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase ;

class SpecialDayDateValidator extends ConstraintValidatorBase 
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

        $days = $this->om->getRepository(SpecialDay::class)->findBy(['year' => $constraint->year->getId()], ['day' => 'ASC']);

        if (!empty($days))
            foreach ($days as $d) {
                if (!$value->contains($d))
                    if (!$d->canDelete()) {
                        $this->context->buildViolation('year.specialDay.error.delete', ['%day%' => $d->getDay()->format('jS M/Y')])
                            ->addViolation();
                        return;
                    }
            }
        foreach($value as $key=> $day) {
            if ($day->getType() == 'alter') {
                $ok = true;
                if (empty($day->getOpen())) {
                    $this->context->buildViolation('year.specialDay.error.timeEmpty')
                        ->addViolation();
                    return ;
                }
                if (empty($day->getStart())) {
                    $this->context->buildViolation('year.specialDay.error.timeEmpty')
                        ->addViolation();
                    return ;
                }
                if (empty($day->getFinish())) {
                    $this->context->buildViolation('year.specialDay.error.timeEmpty')
                        ->addViolation();
                    return ;
                }
                if (empty($day->getClose())) {
                    $this->context->buildViolation('year.specialDay.error.timeEmpty')
                        ->addViolation();
                    return ;
                }
                $time = array(
                    'a' => $day->getOpen(),
                    'b' => $day->getStart(),
                    'c' => $day->getFinish(),
                    'd' => $day->getClose(),
                );
                $ttime = $time;
                asort($ttime);
                if ($time !== $ttime) {
                    $this->context->buildViolation('year.specialDay.error.timeInvalid')
                        ->addViolation();
                    return ;
                }
            }
            if ($key =='__name__' && empty($day->getName()))
                unset($value[$key]);
        }

        return $value ;
    }
}