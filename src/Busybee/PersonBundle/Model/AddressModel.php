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
     * @return array
     */
    public function getBuildingTypeList()
    {
        return $this->buildingTypeList ;
    }

	/**
     * get BuildingType List
     *
     * @return array
     */
    public function getSingleLineAddress()
    {
        return $this->getStreetName();
    }

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
}