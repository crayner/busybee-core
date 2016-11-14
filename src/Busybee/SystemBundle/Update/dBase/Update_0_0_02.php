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
	private $count	= 12 ;
	
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
		$entity->setType('twig');
		$entity->setValue("<pre>{% if buildingType is not empty %}{{ buildingType }} {% endif %}{% if buildingNumber is not empty %}{{ buildingNumber}}/{% endif %}{% if streetNumber is not empty %}{{ streetNumber}} {% endif %}{{ line1 }}
{% if line2 is not empty %}{{ line2 }}
{% endif %}
{{ locality }} {{ territory }} {{ postCode }}
{{ country }}</pre>");
		$entity->setName('Address.Format');
		$entity->setDisplayName('Address Format');
		$entity->setDescription('A template for displaying an address.');
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		// 2
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('twig');
		$entity->setValue("{% if buildingType is not empty %}{{ buildingType }} {% endif %}{% if buildingNumber is not empty %}{{ buildingNumber}}/{% endif %}{% if streetNumber is not empty %}{{ streetNumber}} {% endif %}{{ line1 }}{% if line2 is not empty %} {{ line2 }}{% endif %}");
		$entity->setName('Address.ListLabel');
		$entity->setDisplayName('Address Label List');
		$entity->setDescription('A template to convert the entity values into a string label for autocomplete.');
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));

		$this->sm->saveSetting($entity);
		//3
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
		$entity->setDisplayName('Gender List');
		$entity->setDescription('');
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		//4		
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
		$entity->setDisplayName('List of Titles');
		$entity->setDescription('');
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		//5
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('array');
		$entity->setValue(
			Yaml::dump(array(
				'' => '',
			))
		);
		$entity->setName('Address.TerritoryList');
		$entity->setDisplayName('Territory List');
		$entity->setDescription('List of Territories, States, Provinces or Counties available to addresses in your organisation.');
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		//6
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
		$entity->setDisplayName('Dwelling Type');
		$entity->setDescription("List of building types used as dwellings found in your organisation's area.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		//7
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
		$entity->setDisplayName('Types of Phones');
		$entity->setDescription("List of phone types. The key (key: value) is displayed on your system, and the value is stored in the database.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		//8
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
		$entity->setDisplayName('List of Country Codes');
		$entity->setDescription("List of phone country codes.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		//9
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('regex');
		$entity->setValue("/(^(1300|1800|1900|1902)[0-9]{6}$)|(^0[2|3|4|7|8]{1}[0-9]{8}$)|(^13[0-9]{4}$)/");
		$entity->setName('Phone.Validation');
		$entity->setDisplayName('Phone Validation Rule');
		$entity->setDescription("Phone Validation Regular Expression");
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));

		$this->sm->saveSetting($entity);
		//10
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('twig');
		$entity->setValue("{% set start = phone|slice(0,2) %}
{% set len = phone|length %}
{% if start in [02,03,07,08.09] %}
({{ phone|slice(0,2)}}) {{ phone|slice(2,4)}} {{ phone|slice(6,4)}}{% elseif start in [18,13,04] and len == 10 %}
{{ phone|slice(0,4)}} {{ phone|slice(4,3)}} {{ phone|slice(7,3)}}{% elseif start in [13] and len == 6 %}
{{ phone|slice(0,4)}} {{ phone|slice(4,3)}}{% else %}{{ phone }}{% endif %}");
		$entity->setName('Phone.Display');
		$entity->setDisplayName('Phone Display Format');
		$entity->setDescription("A template to convert phone numbers into display version.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		//11
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('text');
		$entity->setValue("Busybee Institute");
		$entity->setName('Org.Name');
		$entity->setDisplayName('Organisation Name');
		$entity->setDescription("The name of your organisation");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		//12
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('string');
		$entity->setValue('');
		$entity->setName('Org.Ext.Id');
		$entity->setDisplayName('Organisation External Identifier');
		$entity->setDescription("The identifier given to your organisation by your parent or external education authority.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		//13
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('text');
		$entity->setValue('');
		$entity->setName('Postal.Add.1');
		$entity->setDisplayName('Organisation Postal Address Line 1');
		$entity->setDescription("First line of this organisation's postal address.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		//14
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('text');
		$entity->setValue('');
		$entity->setName('Org.Postal.Add.2');
		$entity->setDisplayName('Organisation Postal Address Line 2');
		$entity->setDescription("Second line of this organisation's postal address.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		//15
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('text');
		$entity->setValue('');
		$entity->setName('Org.Postal.Locality');
		$entity->setDisplayName('Organisation Postal Locality');
		$entity->setDescription("Locality of this organisation's postal address. (Town, Suburb or Locality)");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		//16
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('string');
		$entity->setValue('');
		$entity->setName('Org.Postal.Postcode');
		$entity->setDisplayName('Organisation Postal Post Code');
		$entity->setDescription("Post Code of this organisation's postal address.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		//17
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('string');
		$entity->setValue('');
		$entity->setName('Oranisation.Postal.Territory');
		$entity->setDisplayName('Organisation Postal Territory');
		$entity->setDescription("Territory of this organisation's postal address. (State, Province, County)");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		//18
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('text');
		$entity->setValue('');
		$entity->setName('Oganisation.Contact.Name');
		$entity->setDisplayName('Organisation Contact');
		$entity->setDescription("The name of the person to contact in this organisation.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		//19
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('string');
		$entity->setValue('');
		$entity->setName('Org.Contact.Phone');
		$entity->setDisplayName('Organisation Contact Phone Number');
		$entity->setDescription("The phone number of the person to contact in this organisation.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		//20
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('string');
		$entity->setValue('');
		$entity->setName('Org.Contact.Facsimile');
		$entity->setDisplayName('Organisation Contact Facsimile Number');
		$entity->setDescription("The facsimile number of the person to contact in this organisation.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

		$this->sm->saveSetting($entity);
		//21
		$entity = new \Busybee\SystemBundle\Entity\Setting();
		$entity->setType('string');
		$entity->setValue('');
		$entity->setName('Org.Contact.Email');
		$entity->setDisplayName('Organisation Contact Email Address');
		$entity->setDescription("The email address of the person to contact in this organisation.");
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