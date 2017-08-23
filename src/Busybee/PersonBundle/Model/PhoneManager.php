<?php

namespace Busybee\PersonBundle\Model ;

use Busybee\PersonBundle\Entity\Phone;
use Busybee\PersonBundle\Repository\PhoneRepository;
use Symfony\Component\Translation\TranslatorInterface as Translator;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Busybee\PersonBundle\Entity\Address ;

/**
 * Phone Manager
 *
 * @version	8th November 2016
 * @since	28th October 2016
 * @author	Craig Rayner
 */
class PhoneManager
{
	/**
	 * @var	Translator
	 */
	private $trans ;

	/**
	 * @var SettingManager
	 */
	private $sm ;

	/**
	 * @var	PhoneRepository
	 */
	private $pr ;

	/**
	 * Constructor
	 *
	 * @version	8th November 2016
	 * @since	28th October 2016
	 * @param	Translator
	 */
	public function __construct(Translator $trans, SettingManager $sm, PhoneRepository $pr)
	{
		$this->trans = $trans ;
		$this->sm = $sm ;
		$this->pr = $pr ;
	}

	/**
	 * Format Phone
	 *
	 * @version	8th November 2016
	 * @since	8th November 2016
	 * @param	\Busybee\PersonBundle\Entity\Address	$phone
	 * @return	html string
	 */
    public function formatPhone($phone)
    {
		if ($phone instanceof Phone)
			$data =  array(
			    'phoneType' => $phone->getPhoneType(),
				'phoneNumber' => $phone->getPhoneNumber(),
                'countryCode' => $phone->getCountryCode());
		else
			$data =  array(
                'phoneType' => null,
                'phoneNumber' => null,
                'countryCode' => null);
		
		return $this->sm->get('Phone.Format', null, $data);
	}
}