<?php

namespace Busybee\People\LocalityBundle\Model;

use Busybee\Core\SystemBundle\Model\MessageManager;
use Busybee\People\AddressBundle\Entity\Address;
use Busybee\People\LocalityBundle\Entity\Locality;
use Busybee\People\LocalityBundle\Repository\LocalityRepository;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Locality Manager
 *
 * @version    8th November 2016
 * @since      28th October 2016
 * @author     Craig Rayner
 */
class LocalityManager
{
	/**
	 * @var    LocalityRepository
	 */
	private $om;

	/**
	 * @var MessageManager
	 */
	private $messageManager;

	/**
	 * @var Locality|null
	 */
	private $locality = null;

	public function __construct(ObjectManager $om)
	{
		$this->om             = $om;
		$this->messageManager = new MessageManager();
		$this->messageManager->setDomain('BusybeeLocalityBundle');
	}

	/**
	 * @param $id
	 *
	 * @return Locality
	 */
	public function find($id): Locality
	{
		return $this->checkLocality($this->om->getRepository(Locality::class)->find($id));
	}

	/**
	 * Check Locality
	 *
	 * @param Locality|null $locality
	 *
	 * @return Locality
	 */
	private function checkLocality(Locality $locality = null): Locality
	{
		if ($locality instanceof Locality)
		{
			$this->locality = $locality;

			return $this->locality;
		}
		if ($this->locality instanceof Locality)
			return $this->locality;

		$this->locality = new Locality();

		return $this->locality;
	}

	/**
	 * Can Delete
	 *
	 * @param Locality|null $locality
	 *
	 * @todo add Locality Can Delete rules
	 *
	 * @return bool
	 */
	public function canDelete(Locality $locality = null)
	{
		$locality = $this->checkLocality($locality);

		if (intval($this->locality->getId()) < 1)
			return false;

		$result = $this->om->getRepository(Address::class)->createQueryBuilder('a')
			->leftJoin('a.locality', 'l')
			->where('l.id = :loc_id')
			->setParameter('loc_id', $this->locality->getId())
			->getQuery()
			->getResult();;
		if (!empty($result))
			return false;

		return true;
	}

	/**
	 * Add Message
	 *
	 * @inheritdoc
	 *
	 * @return $this
	 */
	public function addMessage($level, $message, $options = [], $domain = null): LocalityManager
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