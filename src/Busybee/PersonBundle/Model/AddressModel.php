<?php

namespace Busybee\PersonBundle\Model ;

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
	 * @var	string
	 */
	protected $classSuffix = '';

	/**
	 * @var	\Busybee\PersonBundle\Repository\LocalityRepository
	 */
	protected $repo;

	/**
	 * @var	array
	 */
	protected $buildingTypeList;

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
}