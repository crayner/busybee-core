<?php

namespace Busybee\Core\CalendarBundle\Model;


abstract class CalendarGroupModel
{
	/**
	 * Can Delete
	 *
	 * @return bool
	 */
	public function canDelete()
	{
		return true;
	}

	/**
	 * Get Full Name
	 *
	 * @return string
	 */
	public function getFullName()
	{
		return $this->getCalendarGroupYear();
	}

	/**
	 * Get Calendar Group Year
	 *
	 * @return string
	 */
	public function getCalendarGroupYear()
	{
		return $this->getNameShort() . ' (' . $this->getYear()->getName() . ')';
	}
}