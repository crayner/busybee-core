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
	private $count	= 7 ;
	
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
		$entity->setValue("<pre>{% if buildingType is not empty %}{{ buildingType }} {% endif %}{% if buildingNumber is not empty %}{{ buildingNumber}}/{% endif %}{% if streetNumber is not empty %}{{ streetNumber}} {% endif %}{{ line1 }}
{% if line2 is not empty %}{{ line2 }}
{% endif %}
{{ locality }} {{ territory }} {{ postCode }}
{{ country }}</pre>");
		$entity->setName('Address.Format');
		$entity->setDescription('A template for displaying an address.');
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		
		$role = $this->em->getRepository('BusybeeSecurityBundle:Role');
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('twig');
		$entity->setValue("{% if buildingType is not empty %}{{ buildingType }} {% endif %}{% if buildingNumber is not empty %}{{ buildingNumber}}/{% endif %}{% if streetNumber is not empty %}{{ streetNumber}} {% endif %}{{ line1 }}{% if line2 is not empty %} {{ line2 }}{% endif %}");
		$entity->setName('Address.ListLabel');
		$entity->setDescription('A template to convert the entity values into a string label for autocomplete.');
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));

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
		$entity->setName('Person.GenderList');
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
		$entity->setName('Person.TitleList');
		$entity->setDescription('List of Titles');
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('array');
		$entity->setValue(
			Yaml::dump(array(
				'' => '',
			))
		);
		$entity->setName('Address.TerritoryList');
		$entity->setDescription('List of Territories, States or Provinces (Counties) available to addresses in your organisation.');
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('array');
		$entity->setValue(
			Yaml::dump(
				array(
					'' => '',
					'Unit' => 'Unit',
					'Apartment' => 'Apt',
					'Town House' => 'TnHs',
				)
			)
		);
		$entity->setName('Address.BuildingType');
		$entity->setDescription("List of building types found in your organisation's area.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('array');
		$entity->setValue(
			Yaml::dump(
				array(
					'Home' => 'Home',
					'Mobile' => 'Mobile',
					'Work' => 'Work',
				)
			)
		);
		$entity->setName('Phone.TypeList');
		$entity->setDescription("List of phone types.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		
		$this->sm->saveSetting($entity);
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('array');
		$entity->setValue(
			Yaml::dump(
				array(
					'Australia +61' => '+61',
				)
			)
		);
		$entity->setName('Phone.CountryList');
		$entity->setDescription("List of phone country codes.");
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