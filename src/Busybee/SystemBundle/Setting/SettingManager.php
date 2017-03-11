<?php
namespace Busybee\SystemBundle\Setting ;

use Busybee\SecurityBundle\Entity\User;
use Busybee\SystemBundle\Entity\Setting;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Yaml\Yaml ;
use Twig_Error_Syntax ;
use Twig_Error_Runtime ;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType ;
use Busybee\PersonBundle\Validator\Phone ;

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
	private	$currentUser ;
	private $settings ;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->repo = $this->container->get('system.setting.repository');
        $this->settings = array();
        $this->setCurrentUser(null);
    }

	/**
	 * @{inheritdoc}
	 */
	public function getCurrentUser()
	{
		return $this->currentUser ;
	}

	/**
	 * @{inheritdoc}
	 */
	public function setCurrentUser(User $user = null)
	{
		$this->currentUser = $user;

		return $this ;
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
        try {
            $this->setting = $this->repo->findOneByName($name);
        } catch (\Exception $e) {
            return $default;
        }
        if (is_null($this->setting) || is_null($this->setting->getName())) {
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
            case 'system':
		    case 'regex':
			case 'text':
				return $this->setting->getValue();
				break;
			case 'string':
				return strval(mb_substr($this->setting->getValue(), 0, 25));
				break;
			case 'image':
				$value = $this->setting->getValue();
				$appPath = $this->container->getParameter('kernel_root_dir');
				$webPath = realpath($appPath . '/../web/');
				if (! file_exists($webPath.'/'.$value))
					$value = $default;
				return $value ;
				break;
			case 'twig':
				return $this->container->get('twig')->createTemplate($this->setting->getValue())->render($options);
				break;
			case 'boolean':
				return (bool) $this->setting->getValue();
				break;
			case 'time':
				return $this->setting->getValue();
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
	 * set Setting
	 *
	 * @version	30th November 2016
	 * @since	21st October 2016
	 * @param	string	$name
	 * @param	mixed	$value
	 * @return	Setting Manager
	 */
    public function setSetting($name, $value)
    {
        $this->setting = $this->repo->findOneByName($name);
        if (is_null($this->setting) || is_null($this->setting->getName()))
            return $this;
        if (true !== $this->container->get('busybee_security.authorisation.checker')->redirectAuthorisation($this->setting->getRole())) return $this;
		switch ($this->setting->getType())
		{
			case 'string':
				$value =  strval(mb_substr($value, 0, 25));
				break;
			case 'regex':
                if (empty($value)) $value = '/^/';
                $test = preg_match($value, 'qwlrfhfri$wegtiwebnf934htr 5965tb');
                break;
            case 'time':
            case 'image':
            case 'text':
            case 'system':
				break ;
			case 'twig':
                if (is_null($value)) $value = '{{ empty }}';
                try {
                    $x = $this->container->get('twig')->createTemplate($value)->render(array());
                } catch (Twig_Error_Syntax $e) {
                    throw new Twig_Error_Syntax($e->getMessage());
                } catch (Twig_Error_Runtime $e) {
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
        if ($this->validateSetting($value)) {
            $this->setting->setValue($value);
            $em = $this->container->get('doctrine')->getManager();
            $em->persist($this->setting);
            $em->flush();
        }

        return $this;
    }

    /**
     * Validate Setting
     *
     * @version    30th November 2016
     * @since    30th November 2016
     * @param    mixed $value
     * @return    boolean
     */
    public function validateSetting($value)
    {
        return true;
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
	 * Build Form
	 *
	 * @version	30th November 2016
	 * @since	30th November 2016
	 * @param	form	$value
	 * @param	array	$value
	 * @return	form
	 */
    public function buildForm($form, $settings)
    {
		foreach($settings as $name=>$setting)
		{
			$details = $this->repo->findOneByName($setting['setting']);
			$type = null ;
			$options = 				array(
					'data'	=> $details->getValue(),
					'label'	=> $name . ' ( '.$details->getDisplayName().' )',
					'attr'	=>	array(
						'help'	=> $details->getDescription(),
					),
					'validation_groups' => array('Default'),
				);
			$options['constraints'] = array();
			if (isset($setting['blank']) && $setting['blank']) $options['required'] = false ;

			if (isset($setting['length'])) $options['attr']['maxLength'] = $setting['length'] ;
			if (isset($setting['minLength'])) $options['attr']['minLength'] = $setting['minLength'] ;

            if (!empty($details->getChoice()))
			{
				if ( 0 === strpos($details->getChoice(), 'parameter.'))
				{
					$options['choices'] = $this->container->getParameter(str_replace('parameter.', '', $details->getChoice()));
				} else {
					$options['choices'] = $this->get($details->getChoice());
				}
				$type = ChoiceType::class;
			}
			if (! is_null($validator = $details->getValidator()))
			{
				$validator = explode(',', $validator);
				foreach ($validator as $w)
					switch ($w)
					{
						case 'phone.validator':
							array_push($options['constraints'], new Phone(array('groups'=>'Default')));
							break ;
						case 'institute.name.validator':
							array_push($options['constraints'], new InstituteName(array('groups'=>'Default')));
							break ;
						default:
							throw new \Exception('I cannot handle '.$w);
					}
			}
			$form->add(str_replace('.', '_', $details->getName()), $type, $options);
		}
		return $form ;
	}

    /**
     * @param $name
     * @return mixed
     */
    public function getParameter($name)
    {
        return $this->container->getParameter($name);
    }

    /**
     * delete Setting
     *
     * @version	21st October 2016
     * @since	21st October 2016
     * @param	Setting/String
     * @return	SettingManager
     */
    public function deleteSetting($setting)
    {
        if (! $setting instanceof Setting)
        {
            $this->get($setting); // if setting is a string then it is a name of a setting to remove.
            if ($this->setting instanceof Setting)
                $setting = $this->setting;
            else
                return $this;
        }
        $em = $this->container->get('doctrine')->getManager();
        $em->remove($setting);
        $em->flush();
        return $this ;
    }

    /**
     * create Setting
     *
     * @version    21st October 2016
     * @since    21st October 2016
     * @param    Setting
     * @return    SettingManager
     */
    public function createSetting(Setting $setting)
    {
        if (!$this->settingExists($setting->getName())) {
            $em = $this->container->get('doctrine')->getManager();
            $em->persist($setting);
            $em->flush();
        } else {
            $this->set($setting->getName(), $setting->getValue());
        }
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function settingExists($name)
    {
        $setting = $this->repo->findOneByName($name);
        if ($setting instanceof Setting && !is_null($setting->getId()))
            return true;
        return false;
    }

    /**
     * set Setting
     *
     * @version    31st October 2016
     * @since    21st October 2016
     * @param    string $name
     * @param    mixed $value
     * @return    this
     */
    public function set($name, $value)
    {
        return $this->setSetting($name, $value);
    }
}