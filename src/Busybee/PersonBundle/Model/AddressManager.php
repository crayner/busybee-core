<?php

namespace Busybee\PersonBundle\Model ;

use Symfony\Component\Translation\DataCollectorTranslator as Translator;
use Busybee\SystemBundle\Setting\SettingManager ;
use Busybee\PersonBundle\Repository\AddressRepository ;
use Busybee\PersonBundle\Entity\Address ;

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
		if (empty($address['streetName']))
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
		if ($address instanceof Address)
			$data =  array('propertyName' => $address->getPropertyName(), 
				'streetName' => $address->getStreetName(), 'locality' => $address->getLocality()->getLocality(), 'territory' => $address->getLocality()->getTerritory(), 
				'postCode' => $address->getLocality()->getPostCode(), 'country' => $address->getLocality()->getCountryName(), 
				'buildingType' => $address->getBuildingType(), 'buildingNumber' => $address->getBuildingNumber(), 'streetNumber' => $address->getStreetNumber());
		else
			$data =  array('propertyName' => null, 
				'streetName' => null, 'locality' => null, 'territory' => null, 
				'postCode' => null, 'country' => null, 
				'buildingType' => null, 'buildingNumber' => null, 'streetNumber' => null);
		
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
		if ($address instanceof Address)
			$data = array('propertyName' => $address->getPropertyName(), 'streetName' => $address->getStreetName(), 'buildingType' => $address->getBuildingType(), 
				'buildingNumber' => $address->getBuildingNumber(), 'streetNumber' => $address->getStreetNumber());
		else
			$data = array('propertyName' => null, 'streetName' => null, 'buildingType' => null, 
				'buildingNumber' => null, 'streetNumber' => null);

		return trim($this->sm->get('Address.ListLabel', null, $data));
	}
	/**
	 * get Address List
	 *
	 * @version	22nd November 2016
	 * @since	8th November 2016
	 * @param	integer	$locality_id
	 * @return	array	
	 */
    public function getAddressList($locality_id)
    {
		$address = $this->ar->findBy(array('locality' => $locality_id), array('propertyName'=>'ASC', 'streetName'=>'ASC', 'streetNumber'=> 'ASC'));
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