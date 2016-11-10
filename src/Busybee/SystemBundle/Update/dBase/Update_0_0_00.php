<?php 
namespace Busybee\SystemBundle\Update\dBase ;

use Busybee\SystemBundle\Update\UpdateInterface ;

/**
 * Update 0.0.00
 *
 * @version	23rd October 2016
 * @since	23rd October 2016
 * @author	Craig Rayner
 */
Class Update_0_0_00 implements UpdateInterface
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
		//1
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('string');
		$entity->setValue('0.0.00');
		$entity->setName('Version.System');
		$entity->setDisplayName('System Version');
		$entity->setDescription('The version of Busybee currently configured on your system.');
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));

		$this->sm->saveSetting($entity);
		//2
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('string');
		$entity->setValue('0.0.00');
		$entity->setName('Version.Database');
		$entity->setDisplayName('Database Version');
		$entity->setDescription('The version of Busybee Database currently configured on your system.');
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));

		$this->sm->saveSetting($entity);
		//3
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('boolean');
		$entity->setValue(true);
		$entity->setName('Installed');
		$entity->setDisplayName('System Installed');
		$entity->setDescription('A flag showing the system has finished installing.');
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));

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