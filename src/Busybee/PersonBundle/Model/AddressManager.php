<?php

namespace Busybee\PersonBundle\Model ;

use Symfony\Component\Translation\DataCollectorTranslator as Translator;

/**
 * Address Manager
 *
 * @version	28th October 2016
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
	 * Constructor
	 *
	 * @version	28th October 2016
	 * @since	28th October 2016
	 * @param	Translator
	 */
	public function __construct(Translator $trans)
	{
		$this->trans = $trans ;
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
}