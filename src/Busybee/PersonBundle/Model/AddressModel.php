<?php

namespace Busybee\PersonBundle\Model ;

use Busybee\PersonBundle\Entity\Locality ;

/**
 * Address Model
 *
 * @version	8th November 2016
 * @since	31st October 2016
 * @author	Craig Rayner
 */
abstract class AddressModel
{
	/**
	 * @var	\Busybee\PersonBundle\Repository\LocalityRepository
	 */
	protected $repo;

    /**
     * @var	array
     */
    protected $buildingTypeList;

    /**
     * @var	array
     */
    protected $address1_list = array();

    /**
     * @var	array
     */
    protected $address2_list = array();

	/**
     * Construct
     *
     * @return Address
     */
    public function __construct()
    {
		$this->setLocality(new Locality());

		return $this;
    }
	
	/**
     * inject Repo
     *
     * @param \Busybee\PersonBundle\Repository\AddressRepository	$repo
     * @return this
     */
    public function injectRepository(\Busybee\PersonBundle\Repository\AddressRepository $repo)
    {
        $this->repo = $repo;
		return $this;
    }
	
	/**
     * get Repo
     *
     * @return \Busybee\PersonBundle\Repository\AddressRepository
     */
    public function getRepository()
    {
        return $this->repo ;
    }
	
	/**
     * get BuildingType List
     *
     * @return array
     */
    public function getBuildingTypeList()
    {
        return $this->buildingTypeList ;
    }

	/**
     * set BuildingType List
     *
	 * @param	array	$list
     * @return Address
     */
    public function setBuildingTypeList($list)
    {
        $this->buildingTypeList = $list;

		return $this ;
    }

	/**
     * get BuildingType List
     *
     * @return string
     */
    public function getSingleLineAddress()
    {
        return trim($this->getStreetNumber() . ' ' . $this->getStreetName());
    }

    /**
     * can Delete
     *
     * @return boolean
     */
    public function canDelete()
    {
        $x = $this->repo->createQueryBuilder('e')
            ->from('\Busybee\PersonBundle\Entity\Person', 'p')
            ->select('COUNT(p.id)')
            ->where('p.address1 = :address1')
            ->orWhere('p.address2 = :address2')
            ->setParameter('address1', $this->getId())
            ->setParameter('address2', $this->getId())
            ->getQuery()
            ->getSingleScalarResult();
        if (empty($x))
            return true ;
        return false ;
    }
    public function __toString()
    {
        return $this->getStreetName();
    }

}