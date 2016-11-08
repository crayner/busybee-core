<?php

namespace Busybee\PersonBundle\Model ;

use Symfony\Component\Translation\DataCollectorTranslator as Translator;
use Busybee\SystemBundle\Setting\SettingManager ;
use Busybee\PersonBundle\Repository\AddressRepository ;

/**
 * Address Manager
 *
 * @version	8th November 2016
 * @since	28th October 2016
 * @author	Craig Rayner
 */
class AddressManager
{
	/**
	 * @var	Translator
	 */
	private $trans ;

	/**
	 * @var	Busybee\SystemBundle\Setting\SettingManager 
	 */
	private $sm ;

	/**
	 * @var	Busybee\PersonBundle\Repository\AddressRepository
	 */
	private $ar ;

	/**
	 * Constructor
	 *
	 * @version	8th November 2016
	 * @since	28th October 2016
	 * @param	Translator
	 */
	public function __construct(Translator $trans, SettingManager $sm, AddressRepository $ar)
	{
		$this->trans = $trans ;
		$this->sm = $sm ;
		$this->ar = $ar ;
	}
	
	/**
	 * Test Address
	 *
	 * @version	28th October 2016
	 * @since	28th October 2016
	 * @param	array	$address
	 * @return	array	Results
	 */
	public function testAddress($address)
	{
		$result = array();
		$result['message'] = '';
		$result['status'] = 'success';
		if (empty($address['line1']))
		{
			$result['message'] = $this->trans->trans('address.test.empty', array(), 'BusybeePersonBundle');
			$result['status'] = 'warning';
		}
		return $result ;
	}
	
	/**
	 * Format Address
	 *
	 * @version	8th November 2016
	 * @since	8th November 2016
	 * @param	busybee\PersonBundle\Entity\Address	$address
	 * @return	html string
	 */
    public function formatAddress($address)
    {
		$data =  array('line1' => $address->getLine1(), 
			'line2' => $address->getLine2(), 'locality' => $address->localityRecord->getLocality(), 'territory' => $address->localityRecord->getTerritory(), 
			'postCode' => $address->localityRecord->getPostCode(), 'country' => $address->localityRecord->getCountryName(), 
			'buildingType' => $address->getBuildingType(), 'buildingNumber' => $address->getBuildingNumber(), 'streetNumber' => $address->getStreetNumber());
		
		return $this->sm->get('Address.Format', null, $data);
	}
	
	/**
	 * get Address List Label
	 *
	 * @version	8th November 2016
	 * @since	8th November 2016
	 * @param	busybee\PersonBundle\Entity\Address	$address
	 * @return	html string
	 */
    public function getAddressListLabel($address)
    {
		$data = array('line1' => $address->getLine1(), 'line2' => $address->getLine2(), 'buildingType' => $address->getBuildingType(), 
			'buildingNumber' => $address->getBuildingNumber(), 'streetNumber' => $address->getStreetNumber());

		return trim($this->sm->get('Address.ListLabel', null, $data));
	}
	/**
	 * get Address List
	 *
	 * @version	8th November 2016
	 * @since	8th November 2016
	 * @param	integer	$locality_id
	 * @return	array	
	 */
    public function getAddressList($locality_id)
    {
		$address = $this->ar->findBy(array('locality' => $locality_id), array('streetNumber'=> 'ASC', 'line1'=>'ASC', 'line2'=>'ASC'));
		$addressList = array();
		if (is_array($address))
			foreach($address as $xx)
			{
				$x = array();
				$x['label'] = $this->getAddressListLabel($xx);
				$x['value'] = $xx->getId();
				$addressList[] = $x;
			}
		return $addressList ;
	}
}