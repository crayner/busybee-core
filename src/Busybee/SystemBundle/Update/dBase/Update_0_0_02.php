<?php 
namespace Busybee\SystemBundle\Update\dBase ;

use Busybee\SystemBundle\Update\UpdateInterface ;
use Symfony\Component\Yaml\Yaml ;
use Busybee\SystemBundle\Entity\Setting ;

/**
 * Update 0.0.02
 *
 * @version	7th February 2017
 * @since	23rd October 2016
 * @author	Craig Rayner
 */
class Update_0_0_02 implements UpdateInterface
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
    private $count = 46;
	
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
		$entity = new Setting();
		$entity->setType('twig');
		$entity->setName('Address.Format');
		$entity->setDisplayName('Address Format');
		$entity->setDescription('A template for displaying an address.');
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		// 2
		$entity = new Setting();
		$entity->setType('twig');
		$entity->setName('Address.ListLabel');
		$entity->setDisplayName('Address Label List');
		$entity->setDescription('A template to convert the entity values into a string label for autocomplete.');
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));

        $this->sm->createSetting($entity);
		//3
		$entity = new Setting();
		$entity->setType('array');
		$entity->setName('Person.GenderList');
		$entity->setDisplayName('Gender List');
		$entity->setDescription('');
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//4		
		$entity = new Setting();
		$entity->setType('array');
		$entity->setName('Person.TitleList');
		$entity->setDisplayName('List of Titles');
		$entity->setDescription('');
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//5
		$entity = new Setting();
		$entity->setType('array');
		$entity->setName('Address.TerritoryList');
		$entity->setDisplayName('Territory List');
		$entity->setDescription('List of Territories, States, Provinces or Counties available to addresses in your organisation.');
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//6
		$entity = new Setting();
		$entity->setType('array');
		$entity->setName('Address.BuildingType');
		$entity->setDisplayName('Dwelling Type');
		$entity->setDescription("List of building types used as dwellings found in your organisation's area.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//7
		$entity = new Setting();
		$entity->setType('array');
		$entity->setName('Phone.TypeList');
		$entity->setDisplayName('Types of Phones');
		$entity->setDescription("List of phone types. The key (key: value) is displayed on your system, and the value is stored in the database.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//8
		$entity = new Setting();
		$entity->setType('array');
		$entity->setName('Phone.CountryList');
		$entity->setDisplayName('List of Country Codes');
		$entity->setDescription("List of phone country codes.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//9
		$entity = new Setting();
		$entity->setType('regex');
		$entity->setName('Phone.Validation');
		$entity->setDisplayName('Phone Validation Rule');
		$entity->setDescription("Phone Validation Regular Expression");
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));

        $this->sm->createSetting($entity);
		//10
		$entity = new Setting();
		$entity->setType('twig');
		$entity->setName('Phone.Display');
		$entity->setDisplayName('Phone Display Format');
		$entity->setDescription("A template to convert phone numbers into display version.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//11
		$entity = new Setting();
		$entity->setType('text');
		$entity->setName('Org.Name');
		$entity->setDisplayName('Organisation Name');
		$entity->setDescription("The name of your organisation");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//12
		$entity = new Setting();
		$entity->setType('string');
		$entity->setName('Org.Ext.Id');
		$entity->setDisplayName('Organisation External Identifier');
		$entity->setDescription("The identifier given to your organisation by your parent or external education authority.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//13
		$entity = new Setting();
		$entity->setType('text');
		$entity->setName('Org.Postal.Address.1');
		$entity->setDisplayName('Organisation Postal Address Line 1');
		$entity->setDescription("First line of this organisation's postal address.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//14
		$entity = new Setting();
		$entity->setType('text');
		$entity->setName('Org.Postal.Address.2');
		$entity->setDisplayName('Organisation Postal Address Line 2');
		$entity->setDescription("Second line of this organisation's postal address.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//15
		$entity = new Setting();
		$entity->setType('text');
		$entity->setName('Org.Postal.Locality');
		$entity->setDisplayName('Organisation Postal Locality');
		$entity->setDescription("Locality of this organisation's postal address. (Town, Suburb or Locality)");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//16
		$entity = new Setting();
		$entity->setType('string');
		$entity->setName('Org.Postal.Postcode');
		$entity->setDisplayName('Organisation Postal Post Code');
		$entity->setDescription("Post Code of this organisation's postal address.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//17
		$entity = new Setting();
		$entity->setType('string');
		$entity->setName('Org.Postal.Territory');
		$entity->setDisplayName('Organisation Postal Territory');
		$entity->setDescription("Territory of this organisation's postal address. (State, Province, County)");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));
		$entity->setChoice('Address.TerritoryList');

        $this->sm->createSetting($entity);
		//18
		$entity = new Setting();
		$entity->setType('text');
		$entity->setName('Org.Contact.Name');
		$entity->setDisplayName('Organisation Contact');
		$entity->setDescription("The name of the person to contact in this organisation.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//19
		$entity = new Setting();
		$entity->setType('string');
		$entity->setName('Org.Contact.Phone');
		$entity->setDisplayName('Organisation Contact Phone Number');
		$entity->setDescription("The phone number of the person to contact in this organisation.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));
		$entity->setValidator('phone.validator');

        $this->sm->createSetting($entity);
		//20
		$entity = new Setting();
		$entity->setType('string');
		$entity->setName('Org.Contact.Facsimile');
		$entity->setDisplayName('Organisation Contact Facsimile Number');
		$entity->setDescription("The facsimile number of the person to contact in this organisation.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//21
		$entity = new Setting();
		$entity->setType('string');
		$entity->setName('Org.Contact.Email');
		$entity->setDisplayName('Organisation Contact Email Address');
		$entity->setDescription("The email address of the person to contact in this organisation.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//22
		$entity = new Setting();
		$entity->setType('text');
		$entity->setName('Org.Physical.Address.1');
		$entity->setDisplayName('Organisation Physical Address Line 1');
		$entity->setDescription("First line of this organisation's physical address.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//23
		$entity = new Setting();
		$entity->setType('text');
		$entity->setName('Org.Physical.Address.2');
		$entity->setDisplayName('Organisation Physical Address Line 2');
		$entity->setDescription("Second line of this organisation's physical address.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//24
		$entity = new Setting();
		$entity->setType('text');
		$entity->setName('Org.Physical.Locality');
		$entity->setDisplayName('Organisation Physical Locality');
		$entity->setDescription("Locality of this organisation's physical address. (Town, Suburb or Locality)");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//25
		$entity = new Setting();
		$entity->setType('string');
		$entity->setName('Org.Physical.Postcode');
		$entity->setDisplayName('Organisation Physical Post Code');
		$entity->setDescription("Post Code of this organisation's physical address.");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
		//26
		$entity = new Setting();
		$entity->setType('string');
		$entity->setName('Org.Physical.Territory');
		$entity->setDisplayName('Organisation Physical Territory');
		$entity->setDescription("Territory of this organisation's physical address. (State, Province, County)");
		$entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));
		$entity->setChoice('Address.TerritoryList');

        $this->sm->createSetting($entity);
		//27
		$entity = new Setting();
		$entity->setType('text');
		$entity->setName('CountryType');
		$entity->setDisplayName('Country Type Form Handler');
		$entity->setDescription("Determines how the country details are obtained and stored in the database.");
        $entity->setRole($role->findOneByRole('ROLE_SYSTEM_ADMIN'));

        $this->sm->createSetting($entity);
		//28
		$entity = new Setting();
		$entity->setType('string');
        $entity->setName('firstDayofWeek');
		$entity->setDisplayName('First Day of Week');
		$entity->setDescription('The first day of the week for display purposes.  Monday or Sunday, defaults to Monday.');
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));

        $this->sm->createSetting($entity);
		//29
		$entity = new Setting();
		$entity->setType('array');
		$entity->setName('schoolWeek');
		$entity->setDisplayName('Days in the School Week');
		$entity->setDescription('Defines the list of days that school would normally be open.');
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));

        $this->sm->createSetting($entity);
		//30
		$entity = new Setting();
		$entity->setType('image');
		$entity->setName('Org.Logo');
		$entity->setDisplayName('Organisation Logo');
		$entity->setDescription('The organisation Logo');
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));
		$entity->setValidator('validator.logo');

        $this->sm->createSetting($entity);
		//31
		$entity = new Setting();
		$entity->setType('image');
		$entity->setName('Org.Logo.Transparent');
		$entity->setDisplayName('Organisation Transparent Logo');
		$entity->setDescription('The organisation Logo in a transparent form.  Recommended to be 80% opacity. Only PNG or GIF image formats support transparency.');
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));
		$entity->setValidator('validator.logo');

        $this->sm->createSetting($entity);
		//32
		$entity = new Setting();
		$entity->setType('image');
		$entity->setName('Background.Image');
		$entity->setDisplayName('Background Image');
		$entity->setDescription('Change the background displayed for the site.  The image needs to be a minimum of 1200px width.  You can load an image of 1M size, but the smaller the size the better.');
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));
		$entity->setValidator('validator.background.image');

        $this->sm->createSetting($entity);
		//33
		$entity = new Setting();
		$entity->setType('time');
		$entity->setName('SchoolDay.Open');
		$entity->setDisplayName('School Day Open Time');
		$entity->setDescription('At what time are students allowed on campus?');
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));
		$entity->setValidator(null);

        $this->sm->createSetting($entity);
		//34
		$entity = new Setting();
		$entity->setType('time');
		$entity->setName('SchoolDay.Begin');
		$entity->setDisplayName('School Day Instruction Start Time');
		$entity->setDescription('The time that teaching starts. Students would normally be considered late after this time.');
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));
		$entity->setValidator(null);

        $this->sm->createSetting($entity);
		//35
		$entity = new Setting();
		$entity->setType('time');
		$entity->setName('SchoolDay.Finish');
		$entity->setDisplayName('School Day Instruction Finish Time');
		$entity->setDescription('The time students are released for the day.');
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));
		$entity->setValidator(null);

        $this->sm->createSetting($entity);
		//36
		$entity = new Setting();
		$entity->setType('time');
		$entity->setName('SchoolDay.Close');
		$entity->setDisplayName('School Day Close Time');
		$entity->setDescription('The time the doors of the campus normally close, all after school and school activities finished.');
		$entity->setRole($role->findOneByRole('ROLE_ADMIN'));
		$entity->setValidator(null);

        $this->sm->createSetting($entity);
        //37
        $entity = new Setting();
        $entity->setType('array');
        $entity->setName('Campus.Resource.Type');
        $entity->setDisplayName('Type of Campus Resource');
        $entity->setDescription('Campus resources are spaces used with the Campus, such as classrooms and Storage Rooms.');
        $entity->setRole($role->findOneByRole('ROLE_ADMIN'));
        $entity->setValidator(null);

        $this->sm->createSetting($entity);
        //38
        $entity = new Setting();
        $entity->setType('array');
        $entity->setName('Staff.Categories');
        $entity->setDisplayName('Staff Categories');
        $entity->setDescription('List of the staff Categories.');
        $entity->setRole($role->findOneByRole('ROLE_ADMIN'));
        $entity->setValidator(null);

        $this->sm->createSetting($entity);
        //39
        $entity = new Setting();
        $entity->setType('string');
        $entity->setName('Country.Code');
        $entity->setDisplayName('Phone Country Code');
        $entity->setDescription('Default phone country code.');
        $entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));
        $entity->setValidator(null);

        $this->sm->createSetting($entity);
        //40
        $entity = new Setting();
        $entity->setType('array');
        $entity->setName('Person.Import');
        $entity->setDisplayName('Person Import Defaults');
        $entity->setDescription('Default values added to imported records.');
        $entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));
        $entity->setValidator(null);

        $this->sm->createSetting($entity);
        //40
        $entity = new Setting();
        $entity->setType('twig');
        $entity->setName('Phone.Format');
        $entity->setDisplayName('Phone Full Display Format');
        $entity->setDescription("A template to convert phone numbers into full display version.");
        $entity->setRole($role->findOneByRole('ROLE_REGISTRAR'));

        $this->sm->createSetting($entity);
        //41
        $entity = new Setting();
        $entity->setType('array');
        $entity->setName('Student.Status.List');
        $entity->setDisplayName('Student Status List');
        $entity->setDescription("List of the valid student status(es)");
        $entity->setRole($role->findOneByRole('ROLE_ADMIN'));

        $this->sm->createSetting($entity);
        //42
        $entity = new Setting();
        $entity->setType('array');
        $entity->setName('Student.CareGiver.Relationship.List');
        $entity->setDisplayName('List of Student - Care Giver Relationship');
        $entity->setDescription('List of Student - Care Giver Relationship');
        $entity->setRole($role->findOneByRole('ROLE_ADMIN'));

        $this->sm->createSetting($entity);
        //43
        $entity = new Setting();
        $entity->setType('array');
        $entity->setName('Ethnicity.List');
        $entity->setDisplayName('List of Ethnicities');
        $entity->setDescription('List of Ethnicities.  Uses the Australian Standard to create this list');
        $entity->setRole($role->findOneByRole('ROLE_ADMIN'));

        $this->sm->createSetting($entity);
        //44
        $entity = new Setting();
        $entity->setType('array');
        $entity->setName('Religion.List');
        $entity->setDisplayName('List of Religions');
        $entity->setDescription('List of Religions.  Uses the Australian Standard to create this list');
        $entity->setRole($role->findOneByRole('ROLE_ADMIN'));

        $this->sm->createSetting($entity);
        //45
        $entity = new Setting();
        $entity->setType('array');
        $entity->setName('Residency.List');
        $entity->setDisplayName('List of Residency Status');
        $entity->setDescription('List of Residency Status.  Usually defined by the government.');
        $entity->setRole($role->findOneByRole('ROLE_ADMIN'));

        $this->sm->createSetting($entity);
        //46
        $entity = new Setting();
        $entity->setType('string');
        $entity->setName('Settings.Default.Overwrite');
        $entity->setDisplayName('Setting File Overwrite');
        $entity->setDescription('A file name that allows the update process to change the default settings to match the users pre-set details.');
        $entity->setRole($role->findOneByRole('ROLE_SYSTEM_ADMIN'));

        $this->sm->createSetting($entity);

        return true;
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