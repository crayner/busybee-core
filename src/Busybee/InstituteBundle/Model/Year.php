<?php
namespace Busybee\InstituteBundle\Model ;


abstract class Year
{
	public function canDelete()
	{
        if (!empty($this->getTerms()))
            foreach ($this->getTerms()->toArray() as $term)
                if (!$term->canDelete())
                    return false;
        if (!empty($this->getGrades()))
            foreach ($this->getGrades()->toArray() as $grade)
                if (!$grade->canDelete())
                    return false;
		return true;
	}
}