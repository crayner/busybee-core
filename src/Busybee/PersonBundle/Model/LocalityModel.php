<?php

namespace Busybee\PersonBundle\Model ;

use Busybee\PersonBundle\Entity\Address;
use Symfony\Component\Intl\Intl ;

/**
 * Locality Model
 *
 * @version	31st October 2016
 * @since	31st October 2016
 * @author	Craig Rayner
 */
abstract class LocalityModel
{
	/**
	 * @var	string
	 */
	protected $classSuffix = '';

	/**
	 * @var	\Busybee\PersonBundle\Repository\LocalityRepository
	 */
	protected $repo;

	/**
	 * set classSuffix
	 *
	 * @version	31st October 2016
	 * @since	31st October 2016
	 * @author	Craig Rayner
	 */
	public function setClassSuffix($classSuffix)
	{
		$this->classSuffix = $classSuffix;
		
		return $this ;
	}

	/**
	 * get classSuffix
	 *
	 * @version	31st October 2016
	 * @since	31st October 2016
	 * @author	Craig Rayner
	 */
	public function getClassSuffix()
	{
		return $this->classSuffix ;
	}
	
	/**
     * inject Repo
     *
     * @param \Busybee\PersonBundle\Repository\LocalityRepository	$repo
     * @return this
     */
    public function injectRepository(\Busybee\PersonBundle\Repository\LocalityRepository $repo)
    {
        $this->repo = $repo;
		return $this;
    }
	
	/**
     * get Repo
     *
     * @return \Busybee\PersonBundle\Repository\LocalityRepository
     */
    public function getRepository()
    {
        return $this->repo ;
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
	
	/**
     * get Full Locality
     *
     * @return string
     */
    public function getFullLocality()
    {
        return trim($this->getLocality().' '.$this->getTerritory().' '. $this->getPostCode().' '.$this->getCountryName());
    }

    /**
     * can Delete
     *
     * @return boolean
     */
    public function canDelete()
    {
        $x = $this->repo->createQueryBuilder('e')
            ->from('\Busybee\PersonBundle\Entity\Address', 'a')
            ->select('COUNT(a.id)')
            ->where('a.locality = :localityID')
            ->setParameter('localityID', $this->getId())
            ->getQuery()
            ->getSingleScalarResult();
        if (empty($x))
            return true ;
        return false ;
    }
}