<?php

namespace Busybee\PersonBundle\Model ;

use Busybee\PersonBundle\Entity\Locality ;
use Busybee\PersonBundle\Repository\AddressRepository;

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
	 * @var	\Busybee\PersonBundle\Repository\AddressRepository
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
     * @return AddressModel
     */
    public function __construct()
    {
		$this->setLocality(new Locality());

		return $this;
    }
	
	/**
     * inject Repo
     *
     * @param AddressRepository
     * @return AddressModel
     */
    public function injectRepository(AddressRepository $repo)
    {
        $this->repo = $repo;
		return $this;
    }
	
	/**
     * get Repo
     *
     * @return AddressRepository
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
     * @return AddressModel
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
        return trim($this->getStreetNumber() . ' ' . $this->getStreetName() . ' ' . $this->getLocality()->getName());
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

    /**
     * to String
     *
     * @return mixed
     */
    public function __toString()
    {
        return $this->getStreetName().' '.$this->getLocality()->__toString();
    }

}