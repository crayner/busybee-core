<?php
namespace Busybee\SystemBundle\Setting ;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

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
	 * @version	31st October 2016
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
			if ($e->getErrorCode() !== 1146){
				throw new \Exception($e->getMessage());
			}
		}
		if (is_null($this->setting))
			return $default;
		switch ($this->setting->getType())
		{
			case 'string':
				return strval(mb_substr($this->setting->getValue(), 0, 25));
				break;
			case 'twig':
				return $this->container->get('twig')->createTemplate($this->setting->getValue())->render($options);
				break;
			case 'boolean':
				return (bool) $this->setting->getValue();
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
        $x = $this->getSetting($name, $value);
		if (is_null($this->setting))
			return null;
		$this->container->denyAccessUnlessGranted($this->setting->getRole()->getRole(), null, 'Unable to write this setting!');
		switch ($this->setting->getType())
		{
			case 'string':
				$value =  strval(mb_substr($value, 0, 25));
				break;
			case 'twig':
				break;
			case 'boolean':
				$value = (bool) $value ;
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
}