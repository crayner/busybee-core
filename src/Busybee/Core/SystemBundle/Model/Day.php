<?php
/**
 * Created by PhpStorm.
 * User: craig
 * Date: 8/11/2017
 * Time: 13:48
 */

namespace Busybee\Core\SystemBundle\Model;


class Day
{
	/**
	 * @var bool
	 */
	private $sun = false;
	/**
	 * @var bool
	 */
	private $mon = false;
	/**
	 * @var bool
	 */
	private $tue = false;
	/**
	 * @var bool
	 */
	private $wed = false;
	/**
	 * @var bool
	 */
	private $thu = false;
	/**
	 * @var bool
	 */
	private $fri = false;
	/**
	 * @var bool
	 */
	private $sat = false;

	/**
	 * @return bool
	 */
	public function isSun(): bool
	{
		return $this->sun;
	}

	/**
	 * @param bool $sun
	 *
	 * @return Day
	 */
	public function setSun(bool $sun): Day
	{
		$this->sun = $sun;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isMon(): bool
	{
		return $this->mon;
	}

	/**
	 * @param bool $mon
	 *
	 * @return Day
	 */
	public function setMon(bool $mon): Day
	{
		$this->mon = $mon;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isTue(): bool
	{
		return $this->tue;
	}

	/**
	 * @param bool $tue
	 *
	 * @return Day
	 */
	public function setTue(bool $tue): Day
	{
		$this->tue = $tue;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isWed(): bool
	{
		return $this->wed;
	}

	/**
	 * @param bool $wed
	 *
	 * @return Day
	 */
	public function setWed(bool $wed): Day
	{
		$this->wed = $wed;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isThu(): bool
	{
		return $this->thu;
	}

	/**
	 * @param bool $thu
	 *
	 * @return Day
	 */
	public function setThu(bool $thu): Day
	{
		$this->thu = $thu;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isFri(): bool
	{
		return $this->fri;
	}

	/**
	 * @param bool $fri
	 *
	 * @return Day
	 */
	public function setFri(bool $fri): Day
	{
		$this->fri = $fri;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isSat(): bool
	{
		return $this->sat;
	}

	/**
	 * @param bool $sat
	 *
	 * @return Day
	 */
	public function setSat(bool $sat): Day
	{
		$this->sat = $sat;

		return $this;
	}
}