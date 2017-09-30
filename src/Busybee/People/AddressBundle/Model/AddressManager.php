<?php
namespace Busybee\People\AddressBundle\Model;

use Busybee\Core\SystemBundle\Model\MessageManager;
use Busybee\People\FamilyBundle\Entity\Family;
use Busybee\People\PersonBundle\Entity\Person;
use Doctrine\Common\Persistence\ObjectManager;
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
	 * @var MessageManager
	 */
	private $messageManager;

	/**
	 * @var SettingManager
	 */
	private $sm;

	/**
	 * @var ObjectManager
	 */
	private $om;

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
	public function __construct(SettingManager $sm, ObjectManager $om)
	{
		$this->messageManager = new MessageManager();
		$this->messageManager->setDomain('BusybeeAddressBundle');
		$this->sm = $sm;
		$this->om = $om;
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
		if (empty($address['streetName']))
			$this->addMessage('warning', 'address.test.empty');
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
		$address     = $this->om->getRepository(Address::class)->findBy(array('locality' => $locality_id), array('propertyName' => 'ASC', 'streetName' => 'ASC', 'streetNumber' => 'ASC'));
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
			$data = ['propertyName' => $address->getPropertyName(), 'streetName' => $address->getStreetName(),
			         'buildingType' => $address->getBuildingType(), 'buildingNumber' => $address->getBuildingNumber(),
			         'streetNumber' => $address->getStreetNumber(), 'locality' => $address->getLocality()->getName()];
		else
			$data = ['propertyName'   => null, 'streetName' => null, 'buildingType' => null,
			         'buildingNumber' => null, 'streetNumber' => null, 'locality' => null];

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

		$x = $this->om->getRepository(Person::class)->createQueryBuilder('p')
			->select('COUNT(p.id)')
			->where('p.address1 = :address1')
			->orWhere('p.address2 = :address2')
			->setParameter('address1', $this->address->getId())
			->setParameter('address2', $this->address->getId())
			->getQuery()
			->getSingleScalarResult();
		if (!empty($x))
			return false;

		if ($this->om->getMetadataFactory()->hasMetadataFor(Family::class))
		{
			$x = $this->om->getRepository(Family::class)->createQueryBuilder('p')
				->select('COUNT(p.id)')
				->where('p.address1 = :address1')
				->orWhere('p.address2 = :address2')
				->setParameter('address1', $this->address->getId())
				->setParameter('address2', $this->address->getId())
				->getQuery()
				->getSingleScalarResult();
			if (!empty($x))
				return false;
		}

		return true;
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
		return $this->checkAddress($this->om->getRepository(Address::class)->find($id));
	}

	/**
	 * Add Message
	 *
	 * @inheritdoc
	 *
	 * @return $this
	 */
	public function addMessage($level, $message, $options = [], $domain = null): AddressManager
	{
		$this->messageManager->addMessage($level, $message, $options, $domain);

		return $this;
	}

	/**
	 * @return MessageManager
	 */
	public function getMessageManager(): MessageManager
	{
		return $this->messageManager;
	}
}