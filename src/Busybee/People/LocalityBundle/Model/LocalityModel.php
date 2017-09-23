<?php

namespace Busybee\People\LocalityBundle\Model;

use Symfony\Component\Intl\Intl;

/**
 * Locality Model
 *
 * @version    31st October 2016
 * @since      31st October 2016
 * @author     Craig Rayner
 */
abstract class LocalityModel
{
	/**
	 * @var    string
	 */
	protected $classSuffix = '';

	/**
	 * @var    \Busybee\People\PersonBundle\Repository\LocalityRepository
	 */
	protected $repo;

	/**
	 * get classSuffix
	 *
	 * @version    31st October 2016
	 * @since      31st October 2016
	 * @author     Craig Rayner
	 */
	public function getClassSuffix()
	{
		return $this->classSuffix;
	}

	/**
	 * set classSuffix
	 *
	 * @version    31st October 2016
	 * @since      31st October 2016
	 * @author     Craig Rayner
	 */
	public function setClassSuffix($classSuffix)
	{
		$this->classSuffix = $classSuffix;

		return $this;
	}

	/**
	 * inject Repo
	 *
	 * @param \Busybee\People\PersonBundle\Repository\LocalityRepository $repo
	 *
	 * @return LocalityModel
	 */
	public function injectRepository(\Busybee\People\PersonBundle\Repository\LocalityRepository $repo)
	{
		$this->repo = $repo;

		return $this;
	}

	/**
	 * get Repo
	 *
	 * @return \Busybee\People\PersonBundle\Repository\LocalityRepository
	 */
	public function getRepository()
	{
		return $this->repo;
	}

	/**
	 * can Delete
	 *
	 * @return boolean
	 */
	public function canDelete()
	{
		$x = $this->repo->createQueryBuilder('e')
			->from('\Busybee\People\AddressBundle\Entity\Address', 'a')
			->select('COUNT(a.id)')
			->where('a.locality = :localityID')
			->setParameter('localityID', $this->getId())
			->getQuery()
			->getSingleScalarResult();
		if (empty($x))
			return true;

		return false;
	}

	/**
	 * to String
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->getFullLocality();
	}

	/**
	 * get Full Locality
	 *
	 * @return string
	 */
	public function getFullLocality()
	{
		return str_replace('  ', ' ', trim($this->getName() . ' ' . $this->getTerritory() . ' ' . $this->getPostCode() . ' ' . $this->getCountryName()));
	}

	/**
	 * get Country Name
	 *
	 * @return string
	 */
	public function getCountryName()
	{
		return Intl::getRegionBundle()->getCountryName(strtoupper($this->getCountry()));
	}
}