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
	private $count	= 9 ;
	
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
		
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('regex');
		$entity->setValue("(^1300(| )[0-9]{3}(| )[0-9]{3}$)|(^1800|1900|1902(| )[0-9]{3}(| )[0-9]{3}$)|(^0[2|3|7|8]{1}(| )[0-9]{4}(| )[0-9]{4}$)|(^13(| )[0-9]{4}$)|(^04[0-9]{2,3}(| )[0-9]{3}(| )[0-9]{3}$)");
		$entity->setName('Phone.Validation');
		$entity->setDescription("Phone Validation Regular Expression");
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));

		$this->sm->saveSetting($entity);
		
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('twig');
		$entity->setValue("{% set start = phone|slice(0,2) %}
{% set len = phone|length %}
{% if start in [02,03,07,08.09] %}
({{ phone|slice(0,2)}}) {{ phone|slice(2,4)}} {{ phone|slice(6,4)}}{% elseif start in [18,13,04] and len == 10 %}
{{ phone|slice(0,4)}} {{ phone|slice(4,3)}} {{ phone|slice(7,3)}}{% elseif start in [13] and len == 6 %}
{{ phone|slice(0,4)}} {{ phone|slice(4,3)}}{% else %}{{ phone }}{% endif %}");
		$entity->setName('Phone.Display');
		$entity->setDescription("A template to convert phone numbers into display version.");
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