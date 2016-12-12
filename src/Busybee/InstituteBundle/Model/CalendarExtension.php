<?php

namespace Busybee\InstituteBundle\Model ;

use Busybee\SystemBundle\Setting\SettingManager ;
use Busybee\InstituteBundle\Model\CalendarManager ;
use Busybee\InstituteBundle\Model\Day ;
use Symfony\Component\DependencyInjection\ContainerInterface as Container ;

class CalendarExtension extends \Twig_Extension
{
    /**
     * @var Setting Manager
     */
    private $sm ;
	
    /**
     * @var Calendar Manager
     */
    private $cm ;
		
    /**
     * @var Container
     */
    private $container ;
	
    public function __construct(SettingManager $sm, CalendarManager $cm,Container $container)
    {
        $this->sm = $sm ;
		$this->cm = $cm ;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('get_dayClass', array($this, 'getDayClass')),
            new \Twig_SimpleFunction('test_nextYear', array($this->cm, "testNextYear")),
        );
    }


    public function getDayClass(Day $day, $class = null)
    {	
		return $this->cm->getDayClass($day, $class);
    }

    public function getName()
    {
        return 'calendar_twig_extension';
    }
}