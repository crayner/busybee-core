<?php
namespace Busybee\SystemBundle\Setting ;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Yaml\Yaml ;
use Twig_Error_Syntax ;
use Twig_Error_Runtime ;

/**
 * Setting Manager
 *
 * @version	22nd October 2016
 * @since	20th October 2016
 * @author	Craig Rayner
 */
class SettingManager
{
	private	$repo ;
	private	$container ;
	private	$setting ;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->repo = $this->container->get('system.setting.repository');
    }

	/**
	 * get Setting
	 *
	 * @version	18th November 2016
	 * @since	20th October 2016
	 * @param	string	$name
	 * @param	mixed	$default
	 * @param	array	$options
	 * @return	mixed	Value
	 */
    public function getSetting($name, $default = null, $options = array())
    {
        try{
			$this->setting = $this->repo->findOneByName($name);
		} catch (\Exception $e) {
			return $default;
		}
		if (is_null($this->setting) || is_null($this->setting->getName()))
		{
			if (false === strpos($name, '.'))
				return $default;
			$name = explode('.', $name);
			$last = end($name);
			array_pop($name);
			$value = $this->getSetting(implode('.', $name), $default, $options);
			if (is_array($value) && isset($value[$last]))
				return $value[$last];
			return $default;
		}
		switch ($this->setting->getType())
		{
			case 'regex':
			case 'text':
				return $this->setting->getValue();
				break;
			case 'string':
				return strval(mb_substr($this->setting->getValue(), 0, 25));
				break;
			case 'twig':
				return $this->container->get('twig')->createTemplate($this->setting->getValue())->render($options);
				break;
			case 'boolean':
				return (bool) $this->setting->getValue();
				break;
			case 'array':
				return Yaml::parse($this->setting->getValue());
				break;
			default:
				throw new \Exception('The Setting Type ('.$this->setting->getType().') has not been defined.');
		}
		return $this->setting->getValue();
    }
	
	/**
	 * save Setting
	 *
	 * @version	21st October 2016
	 * @since	21st October 2016
	 * @param	Container	
	 * @return	void
	 */
	public function saveSetting(\Busybee\SystemBundle\Entity\Setting $setting)
	{
		$setting->setUser($this->getCurrentUser());
		if (is_null($this->getCurrentUser()) || true !== ($response = $this->container->get('busybee_security.authorisation.checker')->redirectAuthorisation($setting->getRole()->getRole())))
			return $response;
			
		$em = $this->container->get('doctrine')->getManager();
		$em->persist($setting);
		$em->flush();
	}

	/**
	 * @{inheritdoc}
	 */
	public function getCurrentUser()
	{
		return $this->container->get('security.token_storage')->getToken()->getUser() ?: null;
	}

	/**
	 * set Setting
	 *
	 * @version	31st October 2016
	 * @since	21st October 2016
	 * @param	string	$name
	 * @param	mixed	$value
	 * @return	this
	 */
    public function setSetting($name, $value)
    {
        $this->setting = $this->repo->findOneByName($name);
		if (is_null($this->setting) || is_null($this->setting->getName()))
			return null;
		if (true !== $this->container->get('busybee_security.authorisation.checker')->redirectAuthorisation($this->setting->getRole()->getRole())) return $this;
		switch ($this->setting->getType())
		{
			case 'string':
				$value =  strval(mb_substr($value, 0, 25));
				break;
			case 'regex':
				$test = preg_match($value, 'qwlrfhfriwegtiwebnf934htr 5965tb');
				break;
			case 'text':
				break ;
			case 'twig':
				try {
					$x = $this->container->get('twig')->createTemplate($value)->render(array());
				} catch (Twig_Error_Syntax $e)
				{
					throw new Twig_Error_Syntax($e->getMessage());
				} catch (Twig_Error_Runtime $e)
				{
					// Ignore Runtime Errors
				}
				break;
			case 'boolean':
				$value = (bool) $value ;
				break;
			case 'array':
				$value = Yaml::dump($value);
				break;
			default:
				throw new \Exception('The Setting Type ('.$this->setting->getType().') has not been defined.');
		}
		$this->setting->setValue($value);
		$this->saveSetting($this->setting);
		return $this ;
    }

	/**
	 * get Setting
	 *
	 * @version	31st October 2016
	 * @since	20th October 2016
	 * @param	string	$name
	 * @param	mixed	$default
	 * @param	array	$options
	 * @return	mixed	Value
	 */
    public function get($name, $default = null, $options = array())
    {
        return $this->getSetting($name, $default, $options);
    }

	/**
	 * set Setting
	 *
	 * @version	31st October 2016
	 * @since	21st October 2016
	 * @param	string	$name
	 * @param	mixed	$value
	 * @return	this
	 */
    public function set($name, $value)
    {
        return $this->setSetting($name, $value);
    }

	/**
	 * get Form Array Data
	 *
	 * @version	1st Novenber 2016
	 * @since	1st Novenber 2016
	 * @param	string	$name
	 * @param	mixed	$default
	 * @param	array	$options
	 * @return	mixed	Value
	 */
    public function getFormArrayData($name, $default = null, $options = array())
    {
        $x = $this->getSetting($name, $default, $options);
		$y = array();
		foreach($x as $display=>$value)
		{
			$w = array();
			$w['keyValue'] = $value;
			$w['displayName'] = $display;
			$y[] = $w;
		}
		$w = array();
		$w['keyValue'] = '';
		$w['displayName'] = '';
		$y['new'] = $w;
		
		return $y;
    }

	/**
	 * set Form Array Data
	 *
	 * @version	1st Novenber 2016
	 * @since	1st Novenber 2016
	 * @param	array	$value
	 * @return	array
	 */
    public function setFormArrayData($value)
    {
		$result = array();
		foreach($value as $w)
		{
			if (! empty($w['keyValue']))
				$result[$w['displayName']] = $w['keyValue'];
		}
		return $result ;
    }

	/**
	 * increment Version
	 *
	 * @version	20th October 2016
	 * @since	20th October 2016
	 * @param	string	$version
	 * @return	string Version
	 */
    public function incrementVersion($version)
    {
		$v = explode('.', $version);
		if (!isset($v[2])) $v[2] = 0;
		if (!isset($v[1])) $v[1] = 0;
		if (!isset($v[0])) $v[0] = 0;
		while (count($v) > 3)
			array_pop($v);
		$v[2]++;
		if ($v[2] > 99) 
		{
			$v[2] = 0;
			$v[1]++;
		}
		if ($v[1] > 9)
		{
			$v[1] = 0;
			$v[0]++;
		}
		$v[2] = str_pad($v[2], 2, '00', STR_PAD_LEFT);
		return implode('.', $v);
	}

	/**
	 * get Choices
	 *
	 * @version	15th November 2016
	 * @since	15th November 2016
	 * @param	string	$version
	 * @return	array
	 */
    public function getChoices($choice)
    {
		if (0 === strpos($choice, 'parameter.'))
		{
			$name = substr($choice, 10);
			if (false === strpos($name, '.'))
				$list = $this->container->getParameter($name);
			else
			{
				$name = explode('.', $name);
				$list = $this->container->getParameter($name[0]);
				array_shift($name);
				while (! empty($name))
				{
					$key = reset($name);
					$list = $list[$key];
					array_shift($name);
				}
			}
		}
		else
			$list = $this->get($choice);
		return $list;
	}
}