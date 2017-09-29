<?php

namespace Busybee\People\AddressBundle\Model;

use Busybee\Core\SystemBundle\Model\FlashBagManager;
use Busybee\People\PersonBundle\Entity\Person;
use Symfony\Component\Translation\TranslatorInterface as Translator;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Busybee\People\AddressBundle\Repository\AddressRepository;
use Busybee\People\AddressBundle\Entity\Address;

/**
 * Address Manager
 *
 * @version    8th November 2016
 * @since      28th October 2016
 * @author     Craig Rayner
 */
class AddressManager
{
	/**
	 * @var    Translator
	 */
	private $trans;

	/**
	 * @var SettingManager
	 */
	private $sm;

	/**
	 * @var    AddressRepository
	 */
	private $ar;

	/**
	 * @var Address|null
	 */
	private $address = null;

	/**
	 * Constructor
	 *
	 * @version    8th November 2016
	 * @since      28th October 2016
	 *
	 * @param    Translator
	 */
	public function __construct(FlashBagManager $fbm, SettingManager $sm, AddressRepository $ar)
	{
		$this->fbm = $fbm;
		$this->sm  = $sm;
		$this->ar  = $ar;
	}

	/**
	 * Test Address
	 *
	 * @version    28th October 2016
	 * @since      28th October 2016
	 *
	 * @param    array $address
	 *
	 * @return    array    Results
	 */
	public function testAddress($address)
	{
		$result            = array();
		$result['message'] = '';
		$result['status']  = 'success';
		if (empty($address['streetName']))
		{
			$result['message'] = $this->trans->trans('address.test.empty', array(), 'BusybeePersonBundle');
			$result['status']  = 'warning';
		}

		return $result;
	}

	/**
	 * Format Address
	 *
	 * @version    8th November 2016
	 * @since      8th November 2016
	 *
	 * @param    \Busybee\People\AddressBundle\Entity\Address $address
	 *
	 * @return    html string
	 */
	public function formatAddress($address)
	{
		if ($address instanceof Address)
			$data = array('propertyName' => $address->getPropertyName(),
			              'streetName'   => $address->getStreetName(), 'locality' => $address->getLocality()->getName(), 'territory' => $address->getLocality()->getTerritory(),
			              'postCode'     => $address->getLocality()->getPostCode(), 'country' => $address->getLocality()->getCountryName(),
			              'buildingType' => $address->getBuildingType(), 'buildingNumber' => $address->getBuildingNumber(), 'streetNumber' => $address->getStreetNumber());
		else
			$data = array('propertyName' => null,
			              'streetName'   => null, 'locality' => null, 'territory' => null,
			              'postCode'     => null, 'country' => null,
			              'buildingType' => null, 'buildingNumber' => null, 'streetNumber' => null);

		return $this->sm->get('Address.Format', null, $data);
	}

	/**
	 * get Address List
	 *
	 * @version    22nd November 2016
	 * @since      8th November 2016
	 *
	 * @param    integer $locality_id
	 *
	 * @return    array
	 */
	public function getAddressList($locality_id)
	{
		$address     = $this->ar->findBy(array('locality' => $locality_id), array('propertyName' => 'ASC', 'streetName' => 'ASC', 'streetNumber' => 'ASC'));
		$addressList = array();
		if (is_array($address))
			foreach ($address as $xx)
			{
				$x             = array();
				$x['label']    = $this->getAddressListLabel($xx);
				$x['value']    = $xx->getId();
				$addressList[] = $x;
			}

		return $addressList;
	}

	/**
	 * get Address List Label
	 *
	 * @version    8th November 2016
	 * @since      8th November 2016
	 *
	 * @param    \Busybee\People\AddressBundle\Entity\Address $address
	 *
	 * @return    html string
	 */
	public function getAddressListLabel($address)
	{
		if ($address instanceof Address)
			$data = array('propertyName'   => $address->getPropertyName(), 'streetName' => $address->getStreetName(), 'buildingType' => $address->getBuildingType(),
			              'buildingNumber'                                              => $address->getBuildingNumber(), 'streetNumber' => $address->getStreetNumber(), 'locality' => $address->getLocality()->getName());
		else
			$data = array('propertyName'   => null, 'streetName' => null, 'buildingType' => null,
			              'buildingNumber' => null, 'streetNumber' => null, 'locality' => null);

		return trim($this->sm->get('Address.ListLabel', null, $data));
	}


	/**
	 * can Delete
	 *
	 * @return boolean
	 */
	public function canDelete(Address $address = null): bool
	{
		$this->checkAddress($address);

		$x = $this->ar->createQueryBuilder('e')
			->from(Person::class, 'p')
			->select('COUNT(p.id)')
			->where('p.address1 = :address1')
			->orWhere('p.address2 = :address2')
			->setParameter('address1', $this->address->getId())
			->setParameter('address2', $this->address->getId())
			->getQuery()
			->getSingleScalarResult();
		if (empty($x))
			return true;

		return false;
	}

	/**
	 * @param Address|null $address
	 *
	 * @return Address|null
	 */
	private function checkAddress(Address $address = null): Address
	{
		if ($address instanceof Address)
		{
			$this->address = $address;

			return $this->address;
		}
		if ($this->address instanceof Address)
			return $this->address;

		$this->address = new Address();

		return $this->address;
	}

	/**
	 * @param $id
	 *
	 * @return Address
	 */
	public function find($id)
	{
		return $this->checkAddress($this->ar->find($id));
	}
}