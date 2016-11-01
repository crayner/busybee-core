<?php 
namespace Busybee\SystemBundle\Update\dBase ;

use Busybee\SystemBundle\Update\UpdateInterface ;
use Symfony\Component\Yaml\Yaml ;

/**
 * Update 0.0.00
 *
 * @version	23rd October 2016
 * @since	23rd October 2016
 * @author	Craig Rayner
 */
Class Update_0_0_02 implements UpdateInterface
{
	/**
	 * @var	Setting Manager	
	 */
	private $sm ;

	/**
	 * @var	Entity Manager	
	 */
	private $em ;
	
	/**
	 * @var	integer
	 */
	private $count	=	3 ;
	
	/**
	 * Constructor
	 *
	 * @version	23rd October 2016
	 * @since	23rd October 2016
	 * @param	Symfony Container
	 * @return	this
	 */
	public function __construct($sm, $em) 
	{
		$this->sm = $sm ;
		$this->em = $em ;
	}

	/**
	 * build
	 *
	 * @version	23rd October 2016
	 * @since	20th October 2016
	 * @param	string	$version
	 * @return	boolean
	 */
    public function build()
    {
		
		$role = $this->em->getRepository('BusybeeSecurityBundle:Role');
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('twig');
		$entity->setValue("<pre>{{ line1 }}
{% if line2 is not empty %}{{ line2 }}
{% endif %}
{{ locality }} {{ territory }} {{ postCode }}
{{ country }}</pre>");
		$entity->setName('Address.Format');
		$entity->setDescription('A template for displaying an address.');
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('array');
		$entity->setValue(
			Yaml::dump(array(
				'Unspecified' => 'U',
				'Male' => 'M',
				'Female' => 'F',
				'Other' => '0',
			))
		);
		$entity->setName('Gender.List');
		$entity->setDescription('Gender List');
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('array');
		$entity->setValue(
			Yaml::dump(array(
				'' => '',
				'Mr' => 'Mr',
				'Mrs' => 'Mrs',
				'Ms' => 'Ms',
				'Miss' => 'Miss',
				'Dr' => 'Dr',
			))
		);
		$entity->setName('Person.Title');
		$entity->setDescription('List of Titles');
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		
		return true ;
	}
	
	/**
	 * get Count
	 *
	 * @version	23rd October 2016
	 * @since	23rd October 2016
	 * @return	integer
	 */
	public function getCount() 
	{
		return $this->count;
	}
}