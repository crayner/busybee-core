<?php
namespace Busybee\InstituteBundle\Model ;


abstract class Year
{
	public function canDelete()
	{
		if (is_array($this->getTerms()))
			foreach($this->getTerms() as $term)
				if (! $term->canDelete())
					return false ;
		return true;
	}
}